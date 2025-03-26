<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserLoginActivity;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->updateLastActivity($user);
        }

        return $next($request);
    }

    private function updateLastActivity($user)
    {
        $today = Carbon::now()->format('Y-m-d');

        // Get last login entry for today
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
