<?php

namespace App\Exports;

use App\Models\UserLoginActivity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserLoginHistoryDetailsExport implements FromView
{
    protected $user_id;
    protected $date;

    public function __construct($user_id, $date)
    {
        $this->user_id = $user_id;
        $this->date = $date;
    }

    public function view(): View
    {
        $data = UserLoginActivity::where('user_id', $this->user_id)
                        ->whereDate('login_date', $this->date)
                        ->orderBy('login_time', 'desc')
                        ->get(['login_time', 'logout_time', 'login_duration', 'data'])
                        ->map(function ($item) {
                            $item->login_duration = gmdate('H:i:s', $item->login_duration);
                            return $item;
                        });

        // Check if today's latest record has NULL logout_time
        if ($this->date == now()->toDateString()) {
            foreach ($data as $row) {
                if (is_null($row->logout_time)) {
                    $row->logout_time = 'Running';
                    $row->login_duration = 'Running';
                }
            }
        }

        return view('exports.login-history-details', compact('data'));
    }


}
