<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserLoginActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoLogoutInactiveUsers implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $users = User::all(); // Get all users

        foreach ($users as $user) {
            $lastActive = Cache::get('user-last-active-' . $user->id);

            // If user inactive for more than 10 minutes, update logout details
            if ($lastActive && Carbon::parse($lastActive)->diffInMinutes(now()) > 10) {
                $this->updateLogoutDetails($user);
                
                // Remove cache key
                Cache::forget('user-last-active-' . $user->id);
            }
        }
    }

    private function updateLogoutDetails($user)
    {
        $today = Carbon::now()->format('Y-m-d');

        $lastLogin = UserLoginActivity::where('user_id', $user->id)
            ->whereDate('login_date', $today)
            ->latest()
            ->first();

        if ($lastLogin) {
            $lastLogin->logout_time = Carbon::now();
            $loginTime = Carbon::parse($lastLogin->login_time);
            $logoutTime = Carbon::parse($lastLogin->logout_time);

            if ($logoutTime->greaterThan($loginTime)) {
                $lastLogin->login_duration = abs($logoutTime->diffInSeconds($loginTime));
            } else {
                $lastLogin->login_duration = 0;
            }

            $lastLogin->save();
        }
    }
}
