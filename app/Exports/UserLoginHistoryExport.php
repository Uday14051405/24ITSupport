<?php

namespace App\Exports;

use App\Models\UserLoginLog;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserLoginHistoryExport implements FromView
{
    protected $fromDate;
    protected $toDate;

    public function __construct($fromDate = null, $toDate = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function view(): View
    {
        $query = UserLoginLog::select(
            'users.first_name',
            'users.last_name',
            'users.email',
            'user_login_logs.user_status',
            'user_login_logs.log_datetime',
            DB::raw('(SELECT SUM(login_duration) FROM user_login_activities 
                        WHERE user_login_activities.user_id = user_login_logs.user_id 
                        AND DATE(user_login_activities.login_date) = DATE(user_login_logs.log_datetime)) AS total_duration'),
            DB::raw('(SELECT COUNT(*) FROM user_login_activities 
                        WHERE user_login_activities.user_id = user_login_logs.user_id 
                        AND DATE(user_login_activities.login_date) = DATE(user_login_logs.log_datetime)) AS activity_count'),
            DB::raw('(SELECT login_duration FROM user_login_activities 
                        WHERE user_login_activities.user_id = user_login_logs.user_id 
                        AND DATE(user_login_activities.login_date) = DATE(user_login_logs.log_datetime)
                        ORDER BY id DESC LIMIT 1) AS latest_duration')
        )
        ->join('users', 'user_login_logs.user_id', '=', 'users.id');

        // Apply date filter if a date is selected
        if ($this->fromDate) {
            $query->whereBetween('user_login_logs.log_datetime', [$this->fromDate . ' 00:00:00', $this->toDate . ' 23:59:59']);
        }

        $data = $query->orderBy('user_login_logs.id', 'desc')->get();

        // Modify total_duration based on conditions
        foreach ($data as $row) {
            $logDate = Carbon::parse($row->log_datetime)->toDateString();
            $today = Carbon::now()->toDateString();

            // if ($logDate === $today) {
            //     if ($row->activity_count == 1) {
            //         $row->total_duration = 'Running';
            //     } elseif ($row->activity_count > 1 && is_null($row->latest_duration)) {
            //         $row->total_duration = gmdate("H:i:s", $row->total_duration) . ' 🟢';
            //     }
            // } else {
                $row->total_duration = $row->total_duration ? gmdate("H:i:s", $row->total_duration) : '-';
            // }
        }

        return view('exports.login-history', compact('data'));
    }
}