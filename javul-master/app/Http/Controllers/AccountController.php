<?php

namespace App\Http\Controllers;

use App\Models\Alerts;
use App\Models\AreaOfInterest;
use App\Models\City;
use App\Models\Country;
use App\Models\Fund;
use App\Models\JobSkill;
use App\Models\Paypal;
use App\Models\PaypalTransaction;
use App\Models\State;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hashids\Hashids;
use App\Models\ZcashWithdrawRequest;
use Illuminate\Support\FacadesConfig;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $msg_flag = false;
        $msg_val = '';
        $msg_type = '';
        if($request->session()->has('msg_val')){
            $msg_val =  $request->session()->get('msg_val');
            $request->session()->forget('msg_val');
            $msg_flag = true;
            $msg_type = "success";
        }
        view()->share('msg_flag',$msg_flag);
        view()->share('msg_val',$msg_val);
        view()->share('msg_type',$msg_type);
        view()->share('payment_method',env('PAYMENT_METHOD'));

        //Cancel Zcash withdrawal request
        if($request->has('cancel_request') && !empty($request->transaction_id)){
            //update zcash and transaction details with cancel status
            $zcash_transaction = ZcashWithdrawRequest::where('id',$request->transaction_id)->first();
            $zcash_transaction->status = "cancel";
            $zcash_transaction->save();
            $transaction = Transaction::where('id',$zcash_transaction->user_transaction_id)->first();
            $transaction->status = "cancel";
            $transaction->save();
            return response()->json(['success'=>true,'message'=>'Your withdrawal request successfully cancelled.']);
        }


        $countries = Unit::getAllCountryWithFrequent();
        $states = State::where('country_id',Auth::user()->country_id)->pluck('name','id');
        $cities = City::where('state_id',Auth::user()->state_id)->pluck('name','id');
        $job_skills = JobSkill::pluck('skill_name','id')->all();
        $area_of_interest = AreaOfInterest::pluck('title','id')->all();

        view()->share('countries',$countries);
        view()->share('states',$states);
        view()->share('cities',$cities);
        view()->share('job_skills',$job_skills);
        view()->share('area_of_interest',$area_of_interest);
        view()->share('users_skills',explode(",",Auth::user()->job_skills));
        view()->share('users_area_of_interest',explode(",",Auth::user()->area_of_interest));

        // current logged in user available balance
        $creditedBalance = Fund::getUserDonatedFund(Auth::user()->id);
        $debitedBalance = Fund::getUserAwardedFund(Auth::user()->id);
        $availableBalance = $creditedBalance - $debitedBalance;
        view()->share('availableBalance',$availableBalance);

        $users_card=[];
        /*if(!empty(Auth::user()->credit_card_id))
            $users_card= Paypal::getCreditCard(Auth::user()->credit_card_id);*/
        view()->share('users_cards',$users_card);
        $userIDHashID= new Hashids('user id hash',10, Config::get('app.encode_chars'));
        view()->share('user_id_encoded',$userIDHashID->encode(Auth::user()->id));

        $alertsObj = Alerts::where('user_id',Auth::user()->id)->first();
        view()->share('alertsObj',$alertsObj);

        //expiry years of card
        //$expiry_years = SiteConfigs::getCardExpiryYear();
        //view()->share('expiry_years',$expiry_years);

        //Get user withdrawal request list
        $withdrawal_list = Transaction::join('zcash_withdraw_request','zcash_withdraw_request.user_transaction_id','=','transactions.id')
        ->select('zcash_withdraw_request.status as withdrawal_status','zcash_withdraw_request.*')
        ->where('transactions.user_id','=', Auth::user()->id)
        ->get();
        view()->share('withdrawal_list',$withdrawal_list);

        return view('users.my_account');
    }

    /*
     * Get notification alert of user
     *
     */
    public function get_notifications()
    {
        if(Auth::check())
        {
            $notifications= UserNotification::where('user_id',Auth::user()->id)->where('message_read','=',0)->get();
            return view('users.notification_popup',['notifications'=>$notifications]);
        }
        return '<div>No notification found..';
    }

    public function update_notifications(Request $request)
    {
        $id = $request->input('id');
        $notificationObj = UserNotification::where('user_id',Auth::user()->id)->where('message_read','=',0)->where('id',$id)->first();
        if(!empty($notificationObj) && count($notificationObj) > 0){
            UserNotification::find($id)->update(['message_read'=>1]);
        }
        return response()->json(['success'=>true]);
    }

    public function check_user_login(Request $request)
    {
        if($request->ajax() && Auth::check()){
            User::find(Auth::user()->id)->update(['loggedin'=>time()]);
            return response()->json(['success'=>true]);
        }
        return view('error.404');
    }

    public function update_personal_info(Request $request)
    {
        if($request->isMethod('post') && $request->ajax())
        {
            $validator = Validator::make($request->all(), [
            //    'first_name' => 'required',
            //    'last_name' => 'required',
                'email' => 'required|unique:users,email,'.Auth::user()->id,
                'paypal_email' => 'email'
                /*'phone'=>'required|numeric',
                'mobile'=>'required|numeric',
                'country'=>'required',
                'state'=>'required',
                'city'=>'required',
                'address'=>'required',
                'job_skills'=>'required',
                'area_of_interest'=>'required'*/
            ]);

            if ($validator->fails())
                return response()->json(['success'=>false,'errors'=>$validator->errors()]);

//            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

            $short_country_name = Country::find($request->input('country'));
            if(!empty($short_country_name))
                $short_country_name = $short_country_name ->shortname;

            $mobile_number = $request->input('mobile');
            //$mobile_number_error = '';
            $isValid = true;
            if(!empty($mobile_number)) {
                /*try {
                    $mobile_number = $phoneUtil->parse($mobile_number, $short_country_name);
                    $isValid = $phoneUtil->isValidNumber($mobile_number);
                } catch (\libphonenumber\NumberParseException $e) {
                    $mobile_number_error = $e->getMessage();
                }*/

                if(!is_numeric($mobile_number))
                    $isValid=false;
            }else
                $mobile_number=null;

            //if(!empty($mobile_number_error))
              //  return \Response::json(['success'=>false,'errors'=>['mobile'=>$mobile_number_error]]);

            if(!empty($mobile_number)) {
                if (!$isValid)
                    return response()->json(['success' => false, 'errors' => ['mobile' => 'Invalid mobile number']]);
            }

            $job_skills = $request->input('job_skills');
            if(!empty($job_skills))
                $job_skills=implode(",",$job_skills);
            else
                $job_skills='';

            $area_of_interest = $request->input('area_of_interest');
            if(!empty($area_of_interest))
                $area_of_interest=implode(",",$area_of_interest);
            else
                $area_of_interest='';


            $image = $request->input('profilePic');
            $clear_image = str_replace(' ','+',$request->input('profilePic'));

            Auth::user()->first_name=$request->input('first_name');
            Auth::user()->last_name=$request->input('last_name');
            Auth::user()->email=$request->input('email');
            Auth::user()->address=$request->input('address');
            Auth::user()->mobile=$request->input('mobile');
            Auth::user()->phone=$request->input('phone');
            Auth::user()->country_id=$request->input('country');
            Auth::user()->state_id=$request->input('state');
            Auth::user()->city_id=$request->input('city');
            Auth::user()->job_skills=$job_skills;
            Auth::user()->area_of_interest=$area_of_interest;
            Auth::user()->profile_pic=$clear_image;
            Auth::user()->timezone=$request->input('timezone');
            Auth::user()->paypal_email=$request->input('paypal_email');
            Auth::user()->save();
            return response()->json(['success'=>true]);
        }

    }

    public function withdraw(Request $request)
    {
        if(empty(Auth::user()->paypal_email))
        {
            $validator = Validator::make($request->all(), [
                'paypal_email'=> 'required|email'
            ]);
            if ($validator->fails()){
                $errors = $validator->messages()->toArray();
                foreach($errors as $index=>$err)
                    $errors[$index]=$err[0];

                $errors['active'] = 'withdraw';
                return response()->json(['success'=>false,'errors'=>$errors]);
            }
            $paypal_email = $request->input('paypal_email');
        }
        else
            $paypal_email=Auth::user()->paypal_email;


        /*$requestedAmount = $request->input('cc-amount');
        $isCurrency = Helpers::isCurrency($requestedAmount);
        if(!$isCurrency)
            return \Response::json(['success'=>false,'errors'=>['error'=>'Please enter amount correctly.']]);*/

        $checkEmailExist = Paypal::checkEmailExistINPaypal($paypal_email);

        if(!$checkEmailExist['success'] && $checkEmailExist['timeout_error'])
            return response()->json(['success'=>false,'errors'=>['error'=>'Could not connect to Paypal. Please try again later']]);
        else if(!$checkEmailExist['success'])
            return response()->json(['success'=>false,'errors'=>['error'=>'Email does not exist in Paypal']]);
        else if($checkEmailExist['success']){
            Auth::user()->paypal_email = $paypal_email;
            Auth::user()->save();

            $creditedBalance = Fund::getUserDonatedFund(Auth::user()->id);
            $debitedBalance = Fund::getUserAwardedFund(Auth::user()->id);
            $availableBalance = $creditedBalance - $debitedBalance;

            $orderIDHashID= new Hashids('order id hash',10, Config::get('app.encode_chars'));

            $transactionData['created_by'] =Auth::user()->id;
            $transactionData['user_id'] =Auth::user()->id;
            $transactionData['amount'] =$availableBalance;
            $transactionData['comments']='$'.$availableBalance.' withdrawn by '.Auth::user()->first_name.' '.Auth::user()->last_name;
            $transactionData['trans_type'] ='debit';
            $transactionID = Transaction::create($transactionData)->id;


            //transfer requested amount to user on given email id. (paypal)
            $data['paypal_email'] =Auth::user()->paypal_email;
            $data['cc-amount'] =$availableBalance;
            $data['returnURL'] = url('funds/success?type=user&orderID='.$orderIDHashID->encode($transactionID));
            $data['cancelURL'] = url('funds/cancel?type=user&orderID='.$orderIDHashID->encode($transactionID));
            $data['ajax'] = true;

            $payment = Paypal::transferAmountToUser($data);
            $transactionObj = Transaction::find($transactionID);

            if(!$payment['success']){
                $transactionObj->update(['status'=>'cancelled']);
                return response()->json(['success'=>false,'errors'=>['error'=>'Could not connect to Paypal. Please try again later.']]);
            }

            if($payment['success'] && !empty($payment['paymentResponse']))
            {
                if(count($transactionObj) > 0 && !empty($transactionObj))
                {
                    if($payment['status'] == "completed")
                        $payment['status']="approved";
                    $transactionObj->update(['pay_key'=>$payment['paykey'],'status'=>strtolower($payment['status'])]);
                }

                // insert actual paypal response in database
                PaypalTransaction::create([
                    'transaction_id'=>$transactionID,
                    'fund_id'=>null,
                    'donate_paypal_id'=>null,
                    'pay_key'=>$payment['paykey']
                ]);


                $creditedBalance = Transaction::where('user_id',Auth::user()->id)->where('trans_type','credit')->sum('amount');
                $debitedBalance = Transaction::where('user_id',Auth::user()->id)->where('trans_type','debit')->sum('amount');
                $availableBalance = $creditedBalance - $debitedBalance;

                return response()->json(['success'=>true,'availableBalance'=>number_format($availableBalance,2)]);
            }
            else
                $transactionObj->update(['status'=>'cancelled']);
            $transactionObj->update(['status'=>'cancelled']);
            return response()->json(['success'=>false,'errors'=>['error'=>'Something goes wrong. Please try again later.']]);
        }
    }


    public function paypal_email_check(Request $request)
    {
        $email = $request->input('paypal_email');
        $validator = Validator::make($request->all(), [
            'paypal_email'=> 'required|email'
        ]);
        if ($validator->fails())
            return response()->json(['success'=>false,'message'=>'Email is invalid.']);

        $checkEmailExist = Paypal::checkEmailExistINPaypal($email);

        if(!$checkEmailExist['success'] && $checkEmailExist['timeout_error'])
            return response()->json(['success'=>false,'message'=>'Could not connect to Paypal. Please try again later.' ]);
        else if(!$checkEmailExist['success'] && !$checkEmailExist['timeout_error'])
            return response()->json(['success'=>false,'message'=>'Email address does not exist in Paypal.' ]);

        if($checkEmailExist['success'])
            return response()->json(['success'=>true]);
    }

    public function update_creditcard(Request $request)
    {
        $inputData = $request->all();
        $inputData['cc-number'] = str_replace(" ","",$inputData['cc-number']);
        $validator = Validator::make($inputData, [
            'cc-card-type'=>'required',
            'exp_month'=>'required',
            'exp_year'=>'required',
            'cc-number'=>'required|numeric'
        ],[
            'cc-card-type.required'=>'Please select card type',
            'exp_month.required'=>'Please select expire month',
            'exp_year.required'=>'Please select expire year',
            'cc-number.required'=>'Please enter card number',
            'cc-number.numeric'=>'Card number must be numeric'
        ]);

        if ($validator->fails())
            return response()->json(['success'=>false,'errors'=>$validator->errors()]);

        $saveCardResponse = Paypal::saveCard($inputData);
        if($saveCardResponse['success'])
            return response()->json(['success'=>true]);
        else if($saveCardResponse['timeout_error'])
            return response()->json(['success'=>false,'errors'=>['error'=>'Could not connect to Paypal. Please try again later.']]);
    }
    public function logout()
    {

        return redirect('logout');
    }

    public function upload_profile(Request $request)
    {

        if($request->hasFile('profile_pic')){
            $file= $request->file('profile_pic');
            $image_name = null;
            if (count($file) > 0) {
                $userIDHashID= new Hashids('user id hash',10, Config::get('app.encode_chars'));

                $rules = ['profile_pic' => 'required', 'extension' => 'required|in:jpg,png,jpeg','profile_pic' => 'mimes:jpg,jpeg,png'];
                $fileData = ['profile_pic' => $file, 'extension' => strtolower($file->getClientOriginalExtension())];

                // doing the validation, passing post data, rules and the messages
                $validator = Validator::make($fileData, $rules);
                if (!$validator->fails()) {
                    if ($file->isValid()) {
                        $destinationPath = base_path() . '/uploads/user_profile/' . $userIDHashID->encode(Auth::user()->id); // upload path
                        if (!File::exists($destinationPath)) {
                            $oldumask = umask(0);
                            @mkdir($destinationPath, 0775); // or even 01777 so you get the sticky bit set
                            umask($oldumask);
                        }
                        $extension = $file->getClientOriginalExtension(); // getting image extension
                        //$fileName = $task_id.'_'.$index . '.' . $extension; // renaming image
                        $fileName = $userIDHashID->encode(Auth::user()->id) . '.' . $extension; // renaming image
                        $file->move($destinationPath, $fileName); // uploading file to given path

                        $logo_path = $destinationPath . '/' . $fileName;
                        $logo = Image::make($logo_path);
                        $logo->resize(150, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $logo->save($destinationPath . '/'.$fileName);
                        Auth::user()->profile_pic = $fileName;
                        Auth::user()->save();
                        return response()->json(['success'=>true,'filename'=>url('uploads/user_profile/'.$userIDHashID->encode(Auth::user()
                                ->id).'/'.$fileName)]);
                    }
                }
            }
        }
        return response()->json(['success'=>false,'error'=>'No files were processed.']);
    }

    public function remove_profile_pic()
    {
        $userIDHashID= new Hashids('user id hash',10, Config::get('app.encode_chars'));
        $user_id = $userIDHashID->encode(Auth::user()->id);

        File::delete('uploads/user_profile/'.$user_id.'/'.Auth::user()->profile_pic );
        Auth::user()->profile_pic = null;
        Auth::user()->save();
        return response()->json(['success'=>true]);
    }

    public function request_to_transfer_zcash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zcash_address'=>'required'
        ],[
            'zcash_address.required'=>'Please enter Zcash address ',
        ]);

        if ($validator->fails())
            return response()->json(['success'=>false,'errors'=>$validator->errors()]);

        // $creditedBalance = Transaction::where('user_id',Auth::user()->id)->where('trans_type','credit_zcash')->sum('amount');
		// $debitedBalance = Transaction::where('user_id',Auth::user()->id)->where('trans_type','debit_zcash')->where('status','!=','rejected')->sum('amount');
        // $availableBalance = $creditedBalance - $debitedBalance;
        $creditedBalance = Fund::getUserDonatedFund(Auth::user()->id);
        $debitedBalance = Fund::getUserAwardedFund(Auth::user()->id);
        $availableBalance = $creditedBalance - $debitedBalance;

        $zcash_address = $request->zcash_address;

		$transactionData['created_by'] = Auth::user()->id;
		$transactionData['user_id'] = Auth::user()->id;
		$transactionData['amount'] = $availableBalance;
		$transactionData['comments']= "Request for transfer ".$availableBalance." my Zcash amount to this ".$zcash_address." address";
		$transactionData['trans_type'] = 'debit_zcash';
		$transactionData['status'] = 'withdrawal';
        $transactionID = Transaction::create($transactionData)->id;

        $send_request = ZcashWithdrawRequest::create([
            'user_id' => Auth::user()->id,
            'user_transaction_id' => $transactionID,
            'amount' => $availableBalance,
            'zcash_address' => $zcash_address,
            'status' => 'withdrawal',
        ])->id;

        //Sending email user
        $userObj = User::where('id',Auth::user()->id)->first();
        $zcashTransaction = ZcashWithdrawRequest::where('user_transaction_id',$transactionID)->first();
        $subject="Request For Transfer Money";
        $message = "We have received request to transfer money from your account to below details. We will process soon.";
        User::SendWithdrawalRequestEmail($message,$subject,$userObj,$zcashTransaction);
        //Sending email to site admin
        $useremail = $userObj->email;
        $userObj = User::where('id','1')->first();
        $subject= "User Requested To Transfer Money From His Account.";
        $message = $useremail." has requested to transfer money from their account to below details.";
        User::SendWithdrawalRequestEmail($message,$subject,$userObj,$zcashTransaction);
        //Email sending end here

        // $creditedBalance = Transaction::where('user_id',Auth::user()->id)->where('trans_type','credit_zcash')->sum('amount');
        // $debitedBalance = Transaction::where('user_id',Auth::user()->id)->where('trans_type','debit_zcash')->where('status','!=','rejected')->sum('amount');
        // $availableBalance = $creditedBalance - $debitedBalance;
        $creditedBalance = Fund::getUserDonatedFund(Auth::user()->id);
        $debitedBalance = Fund::getUserAwardedFund(Auth::user()->id);
        $availableBalance = $creditedBalance - $debitedBalance;

		if(!empty($send_request)){
			return response()->json(['success'=>true,'message'=>"Request send successfully",'availableBalance'=>$availableBalance]);
		}else{
			return response()->json(['success'=>false,'errors'=>['error'=>'Something goes wrong. Please try again later.']]);
		}
    }


}
