<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserLoginActivity;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = \Auth::user();

        if($user->status == 0) {
            Auth::logout();
            return redirect()->back()->withErrors(['message' =>  __('auth.account_inactive')]);
        }
        if($request->login == 'user_login' && $user->user_type === 'user'){
            return redirect(RouteServiceProvider::FRONTEND);
        } 
        elseif($request->login == 'user_login' && $user->user_type !== 'user') {
            Auth::logout();
            return redirect()->back()->withErrors(['message' => 'You are not allowed to log in from here.']);
        }
        else{
            return redirect(RouteServiceProvider::HOME);
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {

        $user = Auth::user(); // Get the authenticated user

        if ($user) {
            $this->updateLogoutDetails($user); // Update logout details before logging out
        }
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(RouteServiceProvider::FRONTEND);
        // return redirect('/');
    }

    private function updateLogoutDetails($user)
    {
        $today = Carbon::now()->format('Y-m-d');

        // Get last login entry for today
        $lastLogin = UserLoginActivity::where('user_id', $user->id)
            ->whereDate('login_date', $today)
            ->latest()
            ->first();

        if ($lastLogin) {
            $lastLogin->logout_time = Carbon::now(); // Store as full timestamp
            $loginTime = Carbon::parse($lastLogin->login_time); // Ensure correct parsing
            $logoutTime = Carbon::parse($lastLogin->logout_time);

            // Ensure the logout time is after login time
            if ($logoutTime->greaterThan($loginTime)) {
                $lastLogin->login_duration = abs($logoutTime->diffInSeconds($loginTime)); // Always positive
            } else {
                $lastLogin->login_duration = 0; // Fallback in case of incorrect data
            }

            $lastLogin->save();
        }
    }
}
