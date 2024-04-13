<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth',['except'=>['index']]);
    }

    public function error_payment(Request $request){
        $toEmail = 'savan@gmail.com';
        $toName = 'Savan Ambaliya';
        $subject = 'Error : Cancel Adaptive Payment';
        \Mail::send('emails.print', ['data'=> $request->all()], function($message) use ($toEmail,$toName,$subject)
        {
            $message->to($toEmail,$toName)->subject($subject);
            $message->from(Config::get("app.notification_email"), Config::get("app.site_name"));
        });
    }

    public function success_payment(Request $request){
        $toEmail = 'savan@gmail.com';
        $toName = 'Savan Ambaliya';
        $subject = 'Success : Adaptive Payment Successfully';
        \Mail::send('emails.print', ['data'=> $request->all()], function($message) use ($toEmail,$toName,$subject)
        {
            $message->to($toEmail,$toName)->subject($subject);
            $message->from(Config::get("app.notification_email"), Config::get("app.site_name"));
        });
    }

    public function ipn_payment(Request $request){
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode ('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        $toEmail = 'savan@gmail.com';
        $toName = 'Savan Ambaliya';
        $subject = 'IPN : Adaptive Payment';

        \Mail::send('emails.print', ['data'=> $myPost], function($message) use ($toEmail,$toName,$subject)
        {
            $message->to($toEmail,$toName)->subject($subject);
            $message->from(Config::get("app.notification_email"), Config::get("app.site_name"));
        });
    }

    public function ipn_donation(Request $request){

    }
}
