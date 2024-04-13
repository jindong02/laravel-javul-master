<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Hashids\Hashids;
use Illuminate\Support\Facades\Config;
use App\Models\Fund;
use App\Models\ZcashTransaction;
use App\Models\ZcashWebhookData;
use App\Models\Transaction;
use App\Models\ZcashWithdrawRequest;
use Ixudra\Curl\Facades\Curl;

class ZcashController extends Controller
{


    public function check_zcash_payment(Request $request)
    {
        $zcash_address = $request->zcash_address;
        $fundID = $request->fundID;
        $user_transaction_id = $request->user_transaction_id;
        $payment_detail = [];

        if(!empty($zcash_address))
        {

            $payment_detail = ZcashTransaction::where('zcash_address',$zcash_address)->where('fund_id',$fundID)->orWhere('user_transaction_id',$user_transaction_id)->first();

            if(count($payment_detail) > 0 && $payment_detail->status == "success")
            {
                $fund_type = '';
                $fund_detail = Fund::where('id',$payment_detail->fund_id)->first();
                //encode fundid
                $orderIDHashID = new Hashids('order id hash',10,Config::get('app.encode_chars'));
                if(!empty($fundID) && $fundID !== "null")
                {
                    $orderID = $orderIDHashID->encode($fundID);
                    $fund_type = $fund_detail->fund_type;
                }else if(!empty($user_transaction_id) && $user_transaction_id !== "null")
                {
                    $orderID = $orderIDHashID->encode($user_transaction_id);
                    $fund_type = 'user';
                }
                $orderID = $orderID;
                //Create payment success url
                $success_url = url('funds/success')."?payment_method=Zcash&type=".$fund_type."&orderID=".$orderID."&paymentId=".$payment_detail->transaction_id;
                return response()->json(['success'=>true,'success_url'=>$success_url]);
            }else{
                return response()->json(['error'=>false]);
            }
        }else{
            return response()->json(['error'=>false]);
        }
    }

    /**
     * To check ZCash Webhook Notification
     * At here we check that the payment is received or not
     */
    public function webhook_notification(Request $request)
    {
        //$test = json_decode('{"hash":"1202cda5b522f565eb86bd1f7f42471f96dca1b5058e6c454f1f9b5cedd12eb1","transfer":"5bfb8e6536ef5a1306bb151820512c64","coin":"tzec","type":"transfer","state":"confirmed","wallet":"5bf27c22f00302a903cbf9b3bc068182"}',true);
        $notification_data = json_decode(file_get_contents("php://input"),true);//$test;
        //$api_url = "https://test.bitgo.com/api/v2/";
        $api_url = env('ZCASH_API_URL');
        //$accessToken = "v2xd906ab6b1c780d80e92b9bf3320bfdf3d7e16cfadfe5a135c54a684d552decf2";
        $accessToken = env('ZCASH_ACCESS_TOKEN');
        $coin = env('ZCASH_COIN');//"zec";//"tzec";
        $transaction_exits = ZcashWebhookData::where("transaction_id",$notification_data['hash'])->first();


        if(count($notification_data) > 0 && isset($notification_data['coin']) && $notification_data['coin'] == $coin){
            if($notification_data['state'] == "confirmed" && count($transaction_exits) > 0){
                //ZcashWebhookData::where("transaction_id",$notification_data['hash'])->update(['notification_status'=>$notification_data['state']]);
                $transaction_exits->notification_status = $notification_data['state'];
                $transaction_exits->save();
            }else{
                if(count($transaction_exits) == 0){

                    $wallet_id = $notification_data['wallet'];
                    $transfer_list = Curl::to($api_url.$coin."/wallet/".$wallet_id."/transfer")
                    ->withHeader('Content-Type: application/json')
                    ->withHeader('Authorization: Bearer '.$accessToken.'')
                    ->asJsonResponse()
                    ->asJson(true)
                    ->get();

                    $transfer_list = $transfer_list['transfers'];
                    //get transfer object uning notification hash
                    $transaction_data = $transfer_list[array_search($notification_data['hash'], array_column($transfer_list, 'txid'))];

                    //check if transfer status is receive
                    if($transaction_data['type'] == "receive")
                    {
                        $transaction_outputs = $transaction_data['outputs'];
                        $pending_transaction = ZcashTransaction::where('status','=','pending')->get();
                        if(count($pending_transaction) > 0)
                        {
                            foreach($transaction_outputs as $output_address)
                            {
                                foreach($pending_transaction as $tra_address)
                                {
                                    if($output_address['address'] == $tra_address->zcash_address)
                                    {
                                        //update transaction details
                                        $zcashTransaction = ZcashTransaction::where('zcash_address',$tra_address->zcash_address)->first();
                                        $zcashTransaction->transaction_id = $transaction_data['txid'];
                                        $zcashTransaction->amount = $transaction_data['value'];//'amount' => $transaction_data['usd']
                                        $zcashTransaction->status = 'success';
                                        $zcashTransaction->save();
                                        //ZcashTransaction::where('zcash_address',$tra_address->zcash_address)->update(['transaction_id' => $transaction_data['txid'],'amount' => $transaction_data['value'],'status' => 'success']);
                                        //Update fund amount
                                        if(!empty($zcashTransaction->fund_id))
                                        {
                                            $fund_detail = Fund::where('id',$tra_address->fund_id)->first();
                                            $fund_detail->amount = $transaction_data['value'];
                                            $fund_detail->status = 'approved';
                                            $fund_detail->save();
                                            //Fund::where('id',$tra_address->fund_id)->update(['amount'=>$transaction_data['value'],'status'=>'approved']);
                                        }else if(!empty($zcashTransaction->user_transaction_id))
                                        {
                                            $fund_detail = Transaction::where('id',$tra_address->user_transaction_id)->first();
                                            $fund_detail->amount = $transaction_data['value'];
                                            $fund_detail->status = 'approved';
                                            $fund_detail->comments = 'Zcash '.$transaction_data['value'].' donation received';
                                            $fund_detail->save();
                                            //Transaction::where('id',$tra_address->user_transaction_id)->update(['amount'=>$transaction_data['value'],'status'=>'approved']);
                                        }

                                        //add webhook details
                                        ZcashWebhookData::create([
                                            'transaction_id' => $transaction_data['txid'],
                                            'zcash_address' => $tra_address->zcash_address,
                                            'notification_status' => $notification_data['state'],
                                            'notification_data' => json_encode($notification_data),
                                            'transaction_data' => json_encode($transaction_data),
                                        ]);

                                        //Send Email
                                        if(!empty($fund_detail->donated_by_user_id))
                                        {
                                            $userObj = User::where('id',$fund_detail->donated_by_user_id)->first();
                                            //view()->share('userObj',$userObj);
                                            //view()->share('zcashTransaction',$zcashTransaction);
                                            $toEmail = $userObj->email;
                                            $toName= $userObj->first_name.' '.$userObj->last_name;
                                            $subject="Thank You";

                                            //return view('emails.thankyou_for_donation');
                                            \Mail::send('emails.thankyou_for_donation', ['mailFrom'=>'Zcash','userObj'=> $userObj,'zcashTransaction'=>$zcashTransaction, 'report_concern' => false], function($message) use ($toEmail,$toName,$subject)
                                            {
                                                $message->to($toEmail,$toName)->subject($subject);
                                                $message->from(Config::get("app.notification_email"), Config::get("app.site_name"));
                                            });
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }
    }


    public function transfer_zcash(Request $request,$zcash_transaction_id)
    {
        $BITGO_EXPRESS_HOST = "http://javul.org:3080/api/v2/";
        //$api_url = "https://test.bitgo.com/api/v2/";
        $api_url = env('ZCASH_API_URL');
        //$accessToken = "v2xb79955ed5cdc75fa4121ccefd3b302c6c9d89f2be5ce66181f95234cb2dfff56";
        $accessToken = env('ZCASH_ACCESS_TOKEN');
        if(empty($accessToken))
        {
            return response()->json(['error'=>true,'message'=>"Please add access token in env file."]);
        }


        if(!empty($zcash_transaction_id))
        {
            $zcashTransactionIDHashID = new Hashids('btc transaction id hash',10,Config::get('app.encode_chars'));
            $zcash_transaction_id = $zcashTransactionIDHashID->decode($zcash_transaction_id);
            $zcash_transaction_id = $zcash_transaction_id[0];

            $zcash_transaction_detail = ZcashWithdrawRequest::find($zcash_transaction_id);
            $transaction_detail = Transaction::find($zcash_transaction_detail->user_transaction_id);
            $transfer_address = $zcash_transaction_detail->zcash_address;
            $transfer_amount = (int) $zcash_transaction_detail->amount;
            $user_id = $zcash_transaction_detail->user_id;

            //Wallet configuration
            $wallet_id = env('ZCASH_WALLET_ID');
            $coin = env('ZCASH_COIN');//"zec";//"tzec";
            $wallet_pass = env('ZCASH_WALLET_PASSWORD');


            //Start
             /**
             * Processes with 2FA-Token
             * To Unlock api for transaction
             */
            if($request->has('two_faToken') && !empty($request->two_faToken)){
                try{
                    $unlock = Curl::to($api_url."user/unlock")
                    ->withContentType('application/json')
                    ->withHeader('Authorization: Bearer '.$accessToken.'')
                    ->withData( array('otp'=>$request->two_faToken) )
                    ->asJson()
                    ->post();

                    if(!isset($unlock->error)){
                        //process the transaction here
                        $transfer_money = Curl::to($BITGO_EXPRESS_HOST."".$coin."/wallet/".$wallet_id."/sendcoins")
                        ->withContentType('application/json')
                        ->withHeader('Authorization: Bearer '.$accessToken.'')
                        ->withData( array("address"=>$transfer_address,"amount"=>$transfer_amount,"walletPassphrase"=>$wallet_pass) )
                        ->asJson()
                        ->post();

                        $transfer_details = $transfer_money->transfer;
                        $zcash_transaction_detail->status = 'approved';
                        $zcash_transaction_detail->transfer_transaction_id = $transfer_details->txid;
                        $zcash_transaction_detail->transaction_data = json_encode($transfer_details);
                        $zcash_transaction_detail->save();
                        //Send email Transfer Success request
                        $userObj = User::where('id',$zcash_transaction_detail->user_id)->first();
                        $subject="Transfer Success";
                        $message = "Your withdrawal money request has been accepted by site admin and processed please check you account and the are transaction details are below.";
                        User::SendWithdrawalRequestEmail($message,$subject,$userObj,$zcash_transaction_detail);

                        return response()->json(['success'=>true,'message'=>'Your transaction was successfully completed.']);
                    }else{
                        if($unlock->error){
                            if($unlock->error == "incorrect otp")
                                $unlock->error = "Please enter correct otp and try again";
                            return response()->json(['error'=>true,'message'=>$unlock->error]);
                        }
                    }
                }catch(\Exception $e){
                    $error_message = $e->getMessage();
                    return response()->json(['error'=>true,'message'=>$error_message]);
                }
            }
            /* Unlock API End Here */

            //If api already unlocked then process the transaction
            $transfer_money = Curl::to($BITGO_EXPRESS_HOST."".$coin."/wallet/".$wallet_id."/sendcoins")
            ->withContentType('application/json')
            ->withHeader('Authorization: Bearer '.$accessToken.'')
            ->withData( array("address"=>$transfer_address,"amount"=>$transfer_amount,"walletPassphrase"=>$wallet_pass) )
            ->asJson()
            ->post();

            if(isset($transfer_money->error)){
                if(isset($transfer_money->needsUnlock))
                    return response()->json(['error'=>true,'need_to_unlock'=>true,'message'=>$transfer_money->message]);
                else
                    return response()->json(['error'=>true,'message'=>$transfer_money->message]);
            }else{
                $transfer_details = $transfer_money->transfer;
                $zcash_transaction_detail->status = 'approved';
                $zcash_transaction_detail->transfer_transaction_id = $transfer_details->txid;
                $zcash_transaction_detail->transaction_data = json_encode($transfer_details);
                $zcash_transaction_detail->save();
                return response()->json(['success'=>true,'message'=>'Your transaction was successfully completed.']);
            }
            //End
        }
    }

    /**
     * Zcash do cancel user transfer request
     */
    public function cancel_transfer_request(Request $request,$zcash_transaction_id){
        if(!empty($zcash_transaction_id)){
            $zcashTransactionIDHashID = new Hashids('btc transaction id hash',10,Config::get('app.encode_chars'));
            $zcash_transaction_id = $zcashTransactionIDHashID->decode($zcash_transaction_id);
            $zcash_transaction_id = $zcash_transaction_id[0];

            $zcash_transaction_data = ZcashWithdrawRequest::find($zcash_transaction_id);
            if(!empty($zcash_transaction_data)){
              $transaction_data = Transaction::find($zcash_transaction_data->user_transaction_id);
              $transaction_data->status = "rejected";
              $transaction_data->save();

              //Update Zcash Transaction Details Also
              $zcash_transaction_data->status = "rejected";
              $zcash_transaction_data->save();

              //Send email about rejected transfer request
              $userObj = User::where('id',$zcash_transaction_data->user_id)->first();
              $subject="javul.org admin has rejected your transfer request.";
              $message = "Your money transfer request has been rejected by site admin and request details are below.";
              User::SendWithdrawalRequestEmail($message,$subject,$userObj,$zcash_transaction_data);

              return redirect('my_tasks');
            }
        }else
            return redirect('my_tasks');
    }

}
