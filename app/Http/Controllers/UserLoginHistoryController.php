<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLoginLog;
use App\Models\UserLoginActivity;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\UserLoginHistoryExport;
use App\Exports\UserLoginHistoryDetailsExport;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserLoginHistoryController extends Controller
{
    public function index()
    {
        return view('user.login-history');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get selected date or default to today's date
            $fromDate = $request->date ? $request->date : date('Y-m-d');
            $toDate = $request->date2 ? $request->date2 : date('Y-m-d');

            $query = UserLoginLog::select(
                'user_login_logs.id',
                'users.first_name',
                'users.user_type',
                'users.id as user_id_main',
                'users.last_name',
                'users.email',
                'user_login_logs.user_id',
                'user_login_logs.user_status',
                'user_login_logs.log_datetime',
                DB::raw('(SELECT SUM(login_duration) FROM user_login_activities 
                        WHERE user_login_activities.user_id = user_login_logs.user_id 
                        AND DATE(user_login_activities.login_date) = DATE(user_login_logs.log_datetime)) AS total_duration'),
                DB::raw('(SELECT COUNT(*) FROM user_login_activities 
                        WHERE user_login_activities.user_id = user_login_logs.user_id 
                        AND DATE(user_login_activities.login_date) = CURDATE()) AS activity_count'),
                DB::raw('(SELECT login_duration FROM user_login_activities 
                        WHERE user_login_activities.user_id = user_login_logs.user_id 
                        AND DATE(user_login_activities.login_date) = CURDATE() 
                        ORDER BY user_login_activities.id DESC LIMIT 1) AS latest_login_duration')
            )
            ->join('users', 'user_login_logs.user_id', '=', 'users.id')
            ->whereBetween('user_login_logs.log_datetime', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']) // Filter by date
            ->orderBy('user_login_logs.id', 'desc');                
           

            // Apply search filters (only name and email)
            if (!empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%{$searchTerm}%")
                    ->orWhere('users.email', 'LIKE', "%{$searchTerm}%");
                });
            }

            return DataTables::of($query)
                ->addIndexColumn() // Serial No
                ->addColumn('user', function ($row) {
                    if($row->user_type == 'provider'){
                        return '<a href="' . route('provider_info', ['id' => $row->user_id_main]) . '" style="display: block;"><h6 class="m-0">' . $row->first_name . ' ' . $row->last_name . '</h6><span>' . $row->email . '</span></a>';
                    }else{
                        return '<a href="' . route('booking.index') . '" style="display: block;"><h6 class="m-0">' . $row->first_name . ' ' . $row->last_name . '</h6><span>' . $row->email . '</span></a>';
                    }
                    })
                ->addColumn('user_status', function ($row) {
                    return '<span style="text-transform: uppercase;">' . $row->user_status . '</span>';
                })
                ->editColumn('log_datetime', function ($row) {
                    return '<span class="view-details" data-userid="'. $row->user_id.'" data-date="'.date('Y-m-d', strtotime($row->log_datetime)).'" style="cursor: pointer;">' . date('d M Y', strtotime($row->log_datetime)) . '</span>';
                })
                ->editColumn('total_duration', function ($row) {
                    $formattedDuration = $row->total_duration ? gmdate("H:i:s", $row->total_duration) : '-';

                    // if (date('Y-m-d', strtotime($row->log_datetime)) == date('Y-m-d') && $row->activity_count == 1) {
                    //     return 'running';
                    // }

                    // if (date('Y-m-d', strtotime($row->log_datetime)) == date('Y-m-d') && $row->activity_count > 1 && is_null($row->latest_login_duration)) {
                    //     return $formattedDuration . ' 🟢';
                    // }

                    return $formattedDuration;
                })
                ->rawColumns(['user', 'user_status', 'log_datetime', 'total_duration'])
                ->make(true);
        }
    }


    public function getLoginDetails(Request $request)
    {
        $activities = UserLoginActivity::where('user_id', $request->user_id)
            ->whereDate('login_date', $request->date)
            ->orderBy('login_time', 'desc')
            ->get(['login_time', 'logout_time', 'login_duration', 'data']);

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    public function export(Request $request)
    {
        $fileType = $request->input('fileType', 'pdf');
        $fromDate = $request->input('date'); // Get selected date
        $toDate = $request->input('date2');

        // Modify the query in the export class to filter by date
        $exportInstance = new UserLoginHistoryExport($fromDate, $toDate);

        $data = $exportInstance->view()->getData()['data'];

        if ($fileType == 'xlsx') {
            return Excel::download($exportInstance, 'login-history.xlsx');
        } elseif ($fileType == 'csv') {
            return Excel::download($exportInstance, 'login-history.csv');
        } elseif ($fileType == 'html') {
            return response()->view('exports.login-history', ['data' => $data], Response::HTTP_OK, [
                'Content-Disposition' => 'attachment; filename="login-history.html"',
                'Content-Type' => 'text/html',
            ]);
        } elseif ($fileType == 'pdf') {
            $pdf = Pdf::loadView('exports.login-history', ['data' => $data]);
            return $pdf->download('login-history.pdf');
        }

        return back()->with('error', 'Invalid file type selected.');
    }


    public function exportDetails(Request $request)
    {
        $fileType = $request->input('fileType', 'pdf');

        $user = User::find($request->user_id, ['first_name', 'last_name']);

        $exportDetailsInstance = new UserLoginHistoryDetailsExport($request->user_id, $request->login_date);
        $data = $exportDetailsInstance->view()->getData()['data'];

        if ($fileType == 'xlsx') {
            return Excel::download($exportDetailsInstance, $user->first_name . '_' . $user->last_name . '_' . $request->login_date . '.xlsx');
        } elseif ($fileType == 'csv') {
            return Excel::download($exportDetailsInstance, $user->first_name . '_' . $user->last_name . '_' . $request->login_date . '.csv');
        } elseif ($fileType == 'html') {
            return response()->view('exports.login-history-details', ['data' => $data], Response::HTTP_OK, [
                'Content-Disposition' => 'attachment; filename="' . $user->first_name . '_' . $user->last_name . '_' . $request->login_date . '.html"',
                'Content-Type' => 'text/html',
            ]);
        } elseif ($fileType == 'pdf') {
            $pdf = Pdf::loadView('exports.login-history-details', ['data' => $data]);
            return $pdf->download($user->first_name . '_' . $user->last_name . '_' . $request->login_date . '.pdf');
        }

        return back()->with('error', 'Invalid file type selected.');
    }


}
