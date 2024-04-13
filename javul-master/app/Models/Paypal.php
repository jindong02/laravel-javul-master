<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Config;

use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Core\PayPalHttpConnection;
use PayPal\Exception\PayPalConfigurationException;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Exception\PayPalInvalidCredentialException;
use PayPal\Exception\PayPalMissingCredentialException;
use PayPal\Exception\PPConfigurationException;
use PayPal\Exception\PPConnectionException;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction as PaypalTransaction;
use PayPal\Api\RedirectUrls;
use PayPal\Service\AdaptiveAccountsService;
use PayPal\Types\AA\AccountIdentifierType;
use PayPal\Types\AA\GetVerifiedStatusRequest;
use App\CreditCards;
use PayPal\Service\AdaptivePaymentsService;
use PayPal\Types\AP\PaymentDetailsRequest;
use PayPal\Types\AP\PayRequest;
use PayPal\Types\AP\Receiver;
use PayPal\Types\AP\ReceiverList;
use PayPal\Types\AP\SetPaymentOptionsRequest;
use PayPal\Types\Common\RequestEnvelope;



class Paypal extends Model{
/**
 * Save a credit card with paypal
 *
 * This helps you avoid the hassle of securely storing credit
 * card information on your site. PayPal provides a credit card
 * id that you can use for charging future payments.
 *
 * @param array $params	credit card parameters
 */

    public static $apiContext;
    public static $sdkConfig;
    public static function initializeContext(){
        $paypalConfig = Config::get('paypal');
        self::$apiContext = new ApiContext(
            new OAuthTokenCredential(
                $paypalConfig['client_id'],     // ClientID
                $paypalConfig['secret']      // ClientSecret
            )
        );

        self::$apiContext->setConfig($paypalConfig['settings']);


        self::$sdkConfig = [
            "mode" => "sandbox",
            "acct1.UserName" => env('ADAPTIVE_PAYMENT_USERNAME'),
            "acct1.Password" => env('ADAPTIVE_PAYMENT_PASSWORD'),
            "acct1.Signature" => env('ADAPTIVE_PAYMENT_SIGNATURE'),
            "acct1.AppId" => env('ADAPTIVE_PAYMENT_APPID')
        ];
    }
    public static function saveCard($params) {
        self::initializeContext();
        $card = new CreditCard();
        $card->setType($params['cc-card-type']);
        $card->setNumber($params['cc-number']);
        $card->setExpireMonth($params['exp_month']);
        $card->setExpireYear($params['exp_year']);
        $card->setCvv2($params['cc-cvc']);

        $timeoutError= false;
        $error='';
        try{
            $card->create(self::$apiContext);
        }catch(PayPalConnectionException $e){
			$error = "Could not connect to Paypal. Please try again later.";
            $timeoutError= true;
        }catch(PayPalConfigurationException $e){
            $error = $e->getMessage();
        }catch(PayPalInvalidCredentialException $e){
            $error = $e->getMessage();
        }catch(PayPalMissingCredentialException $e){
            $error = $e->getMessage();
        }

        if(!empty($error))
            return ['success'=>false,'timeout_error'=>$timeoutError,'message'=>$error];
        else{
            $card_id = $card->getId();
            //save credit card id into table.
            \Auth::user()->credit_card_id = $card_id;
            \Auth::user()->save();
            CreditCards::insert(\Auth::user()->id,$card_id);
            return ['success'=>true,'card_id'=>$card_id];
        }
    }

    /**
     * credit card id obtained from a previous create API call.
     * @param $cardId
     * @return CreditCard
     */
    public static function getCreditCard($cardId) {
        self::initializeContext();
        $creditCard = [];
        $timeout_error= false;
        try{
            $creditCard = CreditCard::get($cardId, self::$apiContext);
        }catch(PayPalConnectionException $e){
            $error = $e->getMessage();
            $timeout_error = true;
        }catch(PayPalConfigurationException $e){
            $error = $e->getMessage();
        }catch(PayPalInvalidCredentialException $e){
            $error = $e->getMessage();
        }catch(PayPalMissingCredentialException $e){
            $error = $e->getMessage();
        }
        return $creditCard;
    }


    /**
     * Create a payment using a previously obtained
     * credit card id. The corresponding credit
     * card is used as the funding instrument.
     *
     * @param string $creditCardId credit card id
     * @param string $total Payment amount with 2 decimal points
     * @param string $currency 3 letter ISO code for currency
     * @param string $paymentDesc
     *
     * @param $creditCardId
     * @param $total
     * @param $currency 3 letter ISO code for currency
     * @param $paymentDesc
     * @return Payment
     */
    public static function makePaymentUsingCC($creditCardId, $total, $currency, $paymentDesc) {
        self::initializeContext();
        $ccToken = new CreditCardToken();
        $ccToken->setCreditCardId($creditCardId);

        $fi = new FundingInstrument();
        $fi->setCreditCardToken($ccToken);

        $payer = new Payer();
        $payer->setPaymentMethod("credit_card");
        $payer->setFundingInstruments(array($fi));

        // Specify the payment amount.
        $amount = new Amount();
        $amount->setCurrency($currency);
        $amount->setTotal($total);
        // ###Transaction
        // A transaction defines the contract of a
        // payment - what is the payment for and who
        // is fulfilling it. Transaction is created with
        // a `Payee` and `Amount` types
        $transaction = new PaypalTransaction();
        $transaction->setAmount($amount);
        $transaction->setDescription($paymentDesc);
        $transaction->setInvoiceNumber(uniqid());

        $payment = new Payment();
        $payment->setIntent("sale");
        $payment->setPayer($payer);
        $payment->setTransactions(array($transaction));

        $error ='';
        try{
            $payment->create(self::$apiContext);
        }catch (\PayPal\Exception\PayPalConnectionException $e) {
            $error = $e->getMessage();
        }catch(Exception $e){ $error = $e->getMessage();}

        if(!empty($error))
            return ['success'=>false,'error'=>$error ];
        else if(!empty($payment))
            return ['success'=>true,'payment'=>$payment];
        else
            return ['success'=>false,'error'=>$error ];
    }

    /**
     * Create a payment using the buyer's paypal
     * account as the funding instrument. Your app
     * will have to redirect the buyer to the paypal
     * website, obtain their consent to the payment
     * and subsequently execute the payment using
     * the execute API call.
     *
     * @param string $total	payment amount in DDD.DD format
     * @param string $currency	3 letter ISO currency code such as 'USD'
     * @param string $paymentDesc	A description about the payment
     * @param string $returnUrl	The url to which the buyer must be redirected
     * 				to on successful completion of payment
     * @param string $cancelUrl	The url to which the buyer must be redirected
     * 				to if the payment is cancelled
     * @return \PayPal\Api\Payment
     */

    public static function makePaymentUsingPayPal($total, $currency, $paymentDesc, $returnUrl, $cancelUrl) {
        self::initializeContext();
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        // Specify the payment amount.
        $amount = new Amount();
        $amount->setCurrency($currency);
        $amount->setTotal($total);


        $item1 = new Item();
        $item1->setName($paymentDesc)
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setSku("123123") // Similar to `item_number` in Classic API
            ->setPrice($total);

        $itemList = new ItemList();

        $itemList->setItems(array($item1));


        // ###Transaction
        // A transaction defines the contract of a
        // payment - what is the payment for and who
        // is fulfilling it. Transaction is created with
        // a `Payee` and `Amount` types
        $transaction = new PaypalTransaction();
        $transaction->setAmount($amount);
        $transaction->setDescription($paymentDesc);
        $transaction->setItemList($itemList);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl);
        $redirectUrls->setCancelUrl($cancelUrl);

        $payment = new Payment();
        $payment->setRedirectUrls($redirectUrls);
        $payment->setIntent("sale");
        $payment->setPayer($payer);
        $payment->setTransactions(array($transaction));

        $error ='';

        try {
            $payment->create(self::$apiContext);
        }catch (\PayPal\Exception\PayPalConnectionException $e) {
            $error = $e->getMessage();
        }catch(Exception $e){ $error = $e->getMessage();}

        if(!empty($error))
            return ['success'=>false,'error'=>$error];
        elseif(!empty($payment))
            return ['success'=>true,'payment'=>$payment];
        else
            return ['success'=>false,'error'=>'Something goes wrong. Please try again later.'];
    }

    public static function getLink(array $links, $type) {
        foreach($links as $link) {
            if($link->getRel() == $type) {
                return $link->getHref();
            }
        }
        return "";
    }

    public static function parseApiError($errorJson) {
        $msg = '';

        $data = json_decode($errorJson, true);
        if(isset($data['name']) && isset($data['message'])) {
            $msg .= $data['name'] . " : " .  $data['message'] . "<br/>";
        }
        if(isset($data['details'])) {
            $msg .= "<ul>";
            foreach($data['details'] as $detail) {
                $msg .= "<li>" . $detail['field'] . " : " . $detail['issue'] . "</li>";
            }
            $msg .= "</ul>";
        }
        if($msg == '') {
            $msg = $errorJson;
        }
        return $msg;
    }

    /**
     * Completes the payment once buyer approval has been
     * obtained. Used only when the payment method is 'paypal'
     *
     * @param string $paymentId id of a previously created
     * 		payment that has its payment method set to 'paypal'
     * 		and has been approved by the buyer.
     *
     * @param string $payerId PayerId as returned by PayPal post
     * 		buyer approval.
     * @return array
     */
    public static function executePayment($paymentId, $payerId) {
        self::initializeContext();
        $payment = self::getPaymentDetails($paymentId);
        $paymentExecution = new PaymentExecution();
        $paymentExecution->setPayerId($payerId);

        $error ='';
        try{
            $payment = $payment->execute($paymentExecution, self::$apiContext);
        }catch (PayPalConnectionException $e) {
            $error = $e->getMessage();
        }catch(Exception $e){ $error = $e->getMessage();}

        if(!empty($error))
            return ['success'=>false,'error'=>$error];
        elseif(!empty($payment))
            return ['success'=>true,'payment'=>$payment];
        else
            return ['success'=>false,'error'=>'Something goes wrong. Please try again later.'];
    }

    /**
     * Retrieves the payment information based on PaymentID from Paypal APIs
     *
     * @param $paymentId
     *
     * @return Payment
     */
    public static function getPaymentDetails($paymentId) {
        self::initializeContext();
        $payment = Payment::get($paymentId, self::$apiContext);
        return $payment;
    }


    /**
     * Function will returns all credit cards of user.
     * @param $user_id
     * @return array
     */
    public static function getAllCreditCards($user_id){
        self::initializeContext();
        $creditCards = CreditCards::where('user_id',$user_id)->pluck('card_id')->all();
        $all = [];
        if(!empty($creditCards) && count($creditCards) > 0){
            $creditCard = new CreditCard();
            foreach($creditCards as $card){

                if(!empty($card)){
                    try{

                        $cardInfo = $creditCard->get($card,self::$apiContext);
                        if(!empty($cardInfo)){
                            $all[$cardInfo->id]=['number'=>$cardInfo->number,'type'=>$cardInfo->type];
                        }
                    }catch(\PayPal\Exception\PayPalConnectionException  $ex){
                        // connection timeout error
                    }
                }
            }
        }
        return $all;
    }

    public static function checkEmailExistINPaypal($email){
        self::initializeContext();
        $getVerifiedStatus = new GetVerifiedStatusRequest();
        $accountIdentifier=new AccountIdentifierType();
        $accountIdentifier->emailAddress = $email;
        $getVerifiedStatus->accountIdentifier=$accountIdentifier;
        $getVerifiedStatus->matchCriteria = "NONE";
        $error='';
        $timeoutError =false;
        $service  = new AdaptiveAccountsService(self::$sdkConfig);
        try {
            // ## Making API call
            // invoke the appropriate method corresponding to API in service
            // wrapper object
            $response = $service->GetVerifiedStatus($getVerifiedStatus);
        } catch(PPConfigurationException $e){
            $error = $e->getMessage();
        }catch(PPConnectionException $e){
            $error = $e->getMessage();
            $timeoutError = true;
        }catch(\Exception $e){
            $error = $e->getMessage();
        }
        if(!empty($error))
            return ['success'=>false,'timeout_error'=>$timeoutError,'message'=>$error];
        if(!empty($response->userInfo))
            return ['success'=>true,'email'=>$response->userInfo];
        else
            return ['success'=>false,'timeout_error'=>$timeoutError,'message'=>'Something goes wrong. Please try again later.'];

    }

    public static function transferAmountToUser($data){
        self::initializeContext();
        $payRequest = new PayRequest();

        $receiver = array();
        $receiver[0] = new Receiver();
        $receiver[0]->amount = $data['cc-amount'];
        $receiver[0]->email = $data['paypal_email'];

        $receiverList = new ReceiverList($receiver);
        $payRequest->receiverList = $receiverList;
        if(isset($data['ajax']) && $data['ajax'])
            $payRequest->senderEmail = env('PAYPAL_MASTER_ACCOUNT');

        //$payRequest->feesPayer = "SENDER";


        $payRequest->requestEnvelope = 'en_US';
        $payRequest->actionType = "PAY";
        $payRequest->cancelUrl = $data['cancelURL'];
        $payRequest->returnUrl = $data['returnURL'];
        $payRequest->currencyCode = "USD";
        //$payRequest->ipnNotificationUrl = "http://javul.org/notification/ipn_payment";

        $adaptivePaymentsService = new AdaptivePaymentsService(self::$sdkConfig);
        $error = '';
        $timeoutError = false;
        try{
            $payResponse = $adaptivePaymentsService->Pay($payRequest);
        }catch(PPConfigurationException $e){
            $error = $e->getMessage();
        }catch(PPConnectionException $e){
            $error = $e->getMessage();
            $timeoutError = true;
        }catch(Exception $e){
            $error = $e->getMessage();
        }

        if(!empty($error))
            return ['success'=>false,'timeout_error'=>$timeoutError,'message'=>$error];

        if(!empty($payResponse)){
            $ack = strtoupper($payResponse->responseEnvelope->ack);
            if($ack != "SUCCESS")
                return ['success'=>false,'timeout_error'=>$timeoutError,'message'=>"Something goes wrong. Please try again."];
            return ['success'=>true,'paymentResponse'=>$payResponse,'paykey'=>$payResponse->payKey,'status'=>$payResponse->paymentExecStatus];
        }
        else
            return ['success'=>true,'message'=>"Something goes wrong. Please try again."];

        /*$requestEnvelope = new RequestEnvelope("en_US");
        $paymentDetailsRequest = new PaymentDetailsRequest($requestEnvelope);
        $paymentDetailsRequest->payKey = "AP-44M68631YT6240058";
        $adaptivePaymentsService = new AdaptivePaymentsService(self::$sdkConfig);
        $paymentDetailsResponse = $adaptivePaymentsService->PaymentDetails($paymentDetailsRequest);
        dd($paymentDetailsResponse);*/
    }
}
