<?php

namespace App\Http\Controllers\Auth;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers as AuthenticatesUsers;
use Hashids\Hashids;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers, RegistersUsers {
        AuthenticatesUsers::redirectPath insteadof RegistersUsers;
        AuthenticatesUsers::guard insteadof RegistersUsers;
    }


    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Where user redirect after successfully logout
     */
    protected $redirectAfterLogout = '/login';

    public $sweetcaptcha;

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        view()->share('user_login',Auth::check());
        $this->middleware('guest')->except('Logout');

    }

    public function authenticated(Request $request, User $user ) {
        return redirect()->intended($this->redirectPath());
    }

    //Login via Username and Email Address.
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $request->merge([$field => $request->email]);

        if (Auth::attempt($request->only($field, 'password'))){
            // dd($field);
            return redirect()->intended('/');
        }


        return redirect('/login')->withErrors([
            'error' => 'These credentials do not match our records.',
        ]);
    }

}
