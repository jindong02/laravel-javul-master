<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Session;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Routing\UrlGenerator;
use App\Models\ZcashTransaction;
use App\Models\ZcashWebhookData;


class Zcash extends Model
{

    public static function received_donation($payment_details = null){
        //$api_url = "https://test.bitgo.com/api/v2/";
        $api_url = env('ZCASH_API_URL');
        //$accessToken = "v2xd906ab6b1c780d80e92b9bf3320bfdf3d7e16cfadfe5a135c54a684d552decf2";
        $accessToken = env('ZCASH_ACCESS_TOKEN');
        if(empty($accessToken)){
            return array('success'=>false,'message'=>"Please add access token in env file.");
        }
        $coin = env('ZCASH_COIN');//"zec";// "tzec";

        /**
         * API Ref :- https://test.bitgo.com/api/v2/tbtc/wallet
         * url :- https://www.bitgo.com/api/v2/?shell#list-wallets
         * Get list of wallet
         */
        $walletList = Curl::to($api_url.$coin."/wallet")
        ->withHeader('Authorization: Bearer '.$accessToken.'')
        ->asJson()
        ->get();
        if(!isset($walletList->error)){
            $wallet = $walletList->wallets[0];
            $wallet_id = $wallet->id;

            $transfet_list = Curl::to($api_url.$coin."/wallet/".$wallet_id."/transfer")
            ->withHeader('Content-Type: application/json')
            ->withHeader('Authorization: Bearer '.$accessToken.'')
            ->asJsonResponse()
            ->get();

            /**
             * POST /api/v2/:coin/wallet/:id/address
             * https://www.bitgo.com/api/v2/?shell#create-wallet-address
             * Create address
             */
            $get_new_address = Curl::to($api_url.$coin."/wallet/".$wallet_id."/address")
            ->withHeader('Authorization: Bearer '.$accessToken.'')
            ->asJsonResponse()
            ->post();
            if(count($get_new_address) > 0 && isset($get_new_address->address)){
                $created_address = $get_new_address->address;
                $filename = time().'.png';
                $address = QrCode::format('png')->margin(0)->size(150)->generate($created_address,'uploads/qr_codes/'.$filename);
                $file_location = Null;
                if(\File::exists(base_path().'/uploads/qr_codes/'.$filename))
                    $file_location = url('uploads/qr_codes/'.$filename);

                //create zcash transaction
                ZcashTransaction::create([
                    'fund_id' => (isset($payment_details['fundID']))?$payment_details['fundID']:NULL,
                    'user_transaction_id' => (isset($payment_details['transaction_id']))?$payment_details['transaction_id']:NULL,
                    'zcash_address' => $created_address,
                    'status' => 'pending',
                    'qr_code' => 'uploads/qr_codes/'.$filename
                ]);

                return array('success'=>true,'qrcode'=>$file_location,'address'=>$created_address,'fundID'=>(isset($payment_details['fundID']))?$payment_details['fundID']:NULL,'user_transaction_id'=>(isset($payment_details['transaction_id']))?$payment_details['transaction_id']:NULL);
            }else{
                return array('success'=>false,'message'=>"Something goes wrong please try again.",'error_data'=>$get_new_address);
            }
        }else{
            return array('success'=>false,'message'=>$walletList->error,'error_data'=>$walletList);
        }
    }

}
