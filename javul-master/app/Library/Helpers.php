<?php namespace App\Library;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;

class Helpers {

    /**
     * get client IP Address
     */
    public static function get_ip_address() {
        // check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && self::validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check if multiple ips exist in var
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($iplist as $ip) {
                    if (self::validate_ip($ip))
                        return $ip;
                }
            } else {
                if (self::validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && self::validate_ip($_SERVER['HTTP_X_FORWARDED']))
            return $_SERVER['HTTP_X_FORWARDED'];
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && self::validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        if (!empty($_SERVER['HTTP_FORWARDED']) && self::validate_ip($_SERVER['HTTP_FORWARDED']))
            return $_SERVER['HTTP_FORWARDED'];

        // return unreliable ip since all else failed
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Returns an encrypted & utf8-encoded
     */
    public static function encrypt_decrypt($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = '!@#$%^&*';
        $secret_iv = '!@%^#$&*';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
            $output = str_replace(array('+','/','='),array('-','_','.'),$output);
        }
        else if( $action == 'decrypt' ){
            $string = str_replace(array('-','_','.'),array('+','/','='),$string);
            $string = base64_decode($string);
            $output = openssl_decrypt($string, $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    /*
     *  Get Custom Token for App user...
     * */

    public static function generateToken() {
        $token = md5(rand());
        return $token;
    }


    /**
     * @param $url
     * @return mixed
     */
    public static function shortURl($url){
        $username = env('SHORTURL_USERNAME');
        $password = env('SHORTURL_PASSWORD');
        $api_url =  env('SHORTURL_URL');

        $data = [     // Data to POST
            'format'   => 'json',
            'action'   => 'shorturl',
            'keyword'  => substr(str_shuffle(time()."abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5),
            'username' => $username,
            'password' => $password,
            'url'      => $url,
        ];

        $response = Curl::to($api_url)
            ->withData($data)
            ->post();

        // Do something with the result. Here, we return the short URL
        $data = json_decode( $response );
        return $data->shorturl;
    }

    public static function timetostr($ts) {
        if(!ctype_digit($ts)) {
            $ts = strtotime($ts);
        }
        $diff = time() - $ts;
        if($diff == 0) {
            return 'now';
        }
        elseif($diff > 0) {
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 60) return 'just now';
                if($diff < 120) return '<span class="time_digit">1</span> <span class="time_text">minute ago</span>';
                if($diff < 3600) return '<span class="time_digit">'.floor($diff / 60) .'</span> <span class="time_text">minutes ago</span>';
                if($diff < 7200) return '<span class="time_digit">1</span> <span class="time_text">hour ago</span>';
                if($diff < 86400) return '<span class="time_digit">'.floor($diff / 3600) . '</span> <span class="time_text">hours ago</span>';
            }
            if($day_diff == 1) { return 'Yesterday'; }
            if($day_diff < 7) { return '<span class="time_digit">'.$day_diff .'</span>  <span class="time_text">days ago</span>'; }
            if($day_diff < 31) { return '<span class="time_digit">'.ceil($day_diff / 7) . '</span>  <span class="time_text">weeks ago</span>'; }
            if($day_diff < 60) { return 'last month'; }
            return date('F Y', $ts);
        }
        else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 120) { return 'in a minute'; }
                if($diff < 3600) { return '<span class="time_digit">'. floor($diff / 60) . '</span> <span class="time_text">minutes ago</span>'; }
                if($diff < 7200) { return 'in an hour'; }
                if($diff < 86400) { return '<span class="time_digit">'. floor($diff / 3600) . '</span> <span class="time_text"> hours ago</span>'; }
            }
            if($day_diff == 1) { return 'Tomorrow'; }
            if($day_diff < 4) { return date('l', $ts); }
            if($day_diff < 7 + (7 - date('w'))) { return 'next week'; }
            if(ceil($day_diff / 7) < 4) { return '<span class="time_digit"> ' . ceil($day_diff / 7) . '</span> <span class="time_text">weeks ago</span>'; }
            if(date('n', $ts) == date('n') + 1) { return 'next month'; }
            return date('F Y', $ts);
        }
    }

    public static function isCurrency($number)
    {
        return preg_match("/^[0-9]+(?:\.[0-9]{1,2})?$/", $number);
    }
}
