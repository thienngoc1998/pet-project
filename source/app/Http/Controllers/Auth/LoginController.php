<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoginSuccessEvent;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests\RequestLogin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public function getFormLogin(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $title_page = 'Login';
        return view('auth.login',compact('title_page'));
    }

    public function postLogin(RequestLogin $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            event(new UserLoginSuccessEvent(
                Auth::user(),
                get_agent()
            ));

            return redirect()->intended('/');
        }

        return redirect()->back();
    }

    protected function writeUserLoginLog(): void
    {
        $log = get_agent();
        $historyLog = \Auth::user()->log_login;
        $historyLog = json_decode($historyLog,true) ?? [];
        $historyLog[] = $log;
        \DB::table('users')->where('id', \Auth::user()->id)
            ->update([
                'log_login' => json_encode($historyLog)
            ]);
        Log::info($historyLog);
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect()->to('/');
    }
}
