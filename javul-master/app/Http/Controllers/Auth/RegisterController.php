<?php
namespace App\Http\Controllers\Auth;
use App\Models\Alerts;
use App\Models\sweetcaptcha;
use App\Models\User;
use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Foundation\Auth\AuthenticatesUsers as AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
    use RegistersUsers, AuthenticatesUsers{
        AuthenticatesUsers::redirectPath insteadof RegistersUsers;
        AuthenticatesUsers::guard insteadof RegistersUsers;
    }
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */


    protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        view()->share('user_login',Auth::check());
        $this->sweetcaptcha =new  sweetcaptcha(
            env('SWEETCAPTCHA_APP_ID'),
            env('SWEETCAPTCHA_KEY'),
            env('SWEETCAPTCHA_SECRET'),
            public_path('sweetcaptcha.php')
        );
//        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        $this->middleware('guest')->except('Logout');
    }

    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
//            'first_name' => 'required|max:255',
//            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'user_name' => 'required|min:6',
            'g-recaptcha-response-name' => ['required', function (string $attribute, mixed $value, Closure $fail){
            $g_response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'    => \config('services.recaptcha.secret_key'),
                'response'  => $value,
                'remoteip'  => \request()->ip()
            ]);
            if(!$g_response->json('success'))
            {
                $fail("The {$attribute} is invalid");
            }
            }]
            /*'country'=>'required',
            'state'=>'required',
            'city'=>'required',*/
        ]);

        $validator->after(function($validator) use ($data) {
            $name=$data['user_name'];
            $fetch=User::where('username',$name)->count();
            if($fetch > 0)
                $validator->errors()->add('username_duplicate', 'The username already exist in system.');
        });
        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     **/

    protected function create(array $data)
    {
        $userData = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'username' => $data['user_name'],
            'country_id' => 231,
            'state_id' => 3924,
            'city_id' => 43070,
            'password' => bcrypt($data['password']),
            'role'=>'user'
        ]);

        $toEmail = $data['email'];
        $toName= $data['first_name'].' '.$data['last_name'];
        $subject="Welcome to Javul.org";

//        Mail::send('emails.registration', ['userObj'=> $userData, 'report_concern' => false], function($message) use ($toEmail,$toName,$subject)
//        {
//            $message->to($toEmail,$toName)->subject($subject);
//            $message->from(Config::get("app.notification_email"), Config::get("app.site_name"));
//        });

//        $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
//        $user_id = $userIDHashID->encode($userData->id);
//        SiteActivity::create([
//            'user_id'=>$userData->id,
//            'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower($userData->first_name.'_'.$userData->last_name)).'">'
//                .$userData->username.'</a> created an account'
//        ]);

        Alerts::create([
            'user_id' => $userData->id,
            'all' => 0,
            'account_creation' => 0,
            'confirmation_email' => 0,
            'forum_replies' => 1,
            'watched_items' => 1,
            'inbox' => 1,
            'fund_received' => 1,
            'task_management' => 1,
        ]);

        return $userData;

    }

    public function showRegistrationForm()
    {
        view()->share('sweetcaptcha',$this->sweetcaptcha);
        return view('auth.register');
    }

    /**
     * Method will called after login successfully into system
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticated( \Illuminate\Http\Request $request, User $user ) {
        return redirect()->intended($this->redirectPath());
    }

}
