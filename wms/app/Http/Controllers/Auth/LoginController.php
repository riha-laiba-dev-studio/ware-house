<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $request->validate(['email'=>'required|email','password'=>'required']);
        $key = 'login:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            LoginLog::create(['email'=>$request->email,'status'=>'failed','ip_address'=>$request->ip(),'user_agent'=>$request->userAgent()]);
            throw ValidationException::withMessages(['email'=>'Too many login attempts. Try again in '.RateLimiter::availableIn($key).' seconds.']);
        }
        if (Auth::attempt(['email'=>$request->email,'password'=>$request->password,'is_active'=>true], $request->remember)) {
            $request->session()->regenerate();
            RateLimiter::clear($key);
            $user = Auth::user();
            $user->update(['last_login_at'=>now(),'last_login_ip'=>$request->ip()]);
            LoginLog::create(['user_id'=>$user->id,'email'=>$user->email,'status'=>'success','ip_address'=>$request->ip(),'user_agent'=>$request->userAgent()]);
            return redirect()->intended(route('dashboard'));
        }
        RateLimiter::hit($key, 60);
        LoginLog::create(['email'=>$request->email,'status'=>'failed','ip_address'=>$request->ip(),'user_agent'=>$request->userAgent()]);
        throw ValidationException::withMessages(['email'=>'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
