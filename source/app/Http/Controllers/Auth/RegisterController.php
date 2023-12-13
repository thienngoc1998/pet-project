<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestRegister;
use App\Jobs\SendMailAfterUserRegistrationSuccess;
use App\Providers\RouteServiceProvider;
use App\User;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\RegisterSuccess;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getFormRegister()
    {
        $title_page = 'Đăng ký';
        return view('auth.register', compact('title_page'));
    }

    public function postRegister(RequestRegister $request)
    {
        try {
            $user = $request->validated();
            $user['password'] = Hash::make($user['password']);
            $user = User::create($user);
            dispatch(new SendMailAfterUserRegistrationSuccess($user));

            return redirect()->route('get.login');
        } catch (Exception $exception) {
            dd($exception);
            return redirect()->back();
        }
    }
}
