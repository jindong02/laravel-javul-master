<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        view()->share('user_login',\Auth::check());
        $this->middleware('guest');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required']);
        $user = User::where('email', $request->only('email'))->first();

        if(!$user) {
            return redirect()->back()->withErrors(['email' => trans('passwords.user')]);
        }

        $exist = DB::table('password_resets')->where('email', $request->get('email'))->first();
        $token = hash_hmac('sha256', str_random(40), str_random(40));

        if($exist) {
            DB::table('password_resets')
                ->where('email', $user->email)
                ->update(['token' => $token]);
        } else {
            DB::table('password_resets')
                ->insert(['email' => $user->email, 'token' => $token]);
        }

        \Mail::send('auth.emails.password', ['userObj'=> $user, 'token' => $token, 'user' => $user], function($message) use ($user)
        {
            $message->to($user->email, $user->username)->subject('Your password reset link');
            $message->from(config("app.notification_email"), config("app.site_name"));
        });

        session()->flash('status', trans('passwords.sent'));
        return redirect()->back();
    }
}
