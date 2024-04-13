<?php

namespace App\Models;


use Faker\Provider\File;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SiteConfigs extends Model
{
    /**
     * function return unit credibility types for unit creation and updation
     * @param string $type
     * @return array
     */
    public static function getUnitCredibilityTypes($type=''){
       $credibility_types = ['platinum'=>'Platinum','gold'=>'Gold','silver'=>'Silver','bronze'=>'Bronze'];
       if(!empty($type))
           return $credibility_types[$type];
       else
           return $credibility_types;
   }

    public static function task_status($status=''){
        $task_status = ['editable'=>'Editable','awaiting_approval'=>'Awaiting Approval','approval'=>'Approval',
            'open_for_bidding'=>'Open for Bidding','assigned'=>'Assigned','awaiting_assignment'=>'Awaiting Assignment',
            'in_progress'=>'In Progress','completion_evaluation'=>'Completion Evaluation','completed'=>'Completed',
            'cancelled'=>'Cancelled'];
        if(!empty($status) && isset($task_status[$status]))
            return $task_status[$status];
        return $task_status;

    }

    public static function getCardExpiryYear(){
        $currentYear = (int)date('Y');
        $years = [];
        for($i=$currentYear;$i<($currentYear+35);$i++ ){
            $years[$i]=$i;
        }
        return $years;
    }

    public static function get_timezones_list($timezone=false)
    {
        $timezones =
            array(
                'Pacific/Midway' => "Pacific/Midway Island",
                'US/Samoa' => "US/Samoa",
                'US/Hawaii' => "US/Hawaii",
                'US/Alaska' => "US/Alaska",
                'US/Pacific' => "US/Pacific Time (US &amp; Canada)",
                'America/Tijuana' => "America/Tijuana",
                'US/Arizona' => "US/Arizona",
                'US/Mountain' => "US/Mountain Time (US &amp; Canada)",
                'America/Chihuahua' => "America/Chihuahua",
                'America/Mazatlan' => "America/Mazatlan",
                'America/Mexico_City' => "America/Mexico City",
                'America/Monterrey' => "America/Monterrey",
                'Canada/Saskatchewan' => "Canada/Saskatchewan",
                'US/Central' => "US/Central Time (US &amp; Canada)",
                'US/Eastern' => "US/Eastern Time (US &amp; Canada)",
                'US/East-Indiana' => "US/Indiana (East)",
                'America/Bogota' => "America/Bogota",
                'America/Lima' => "America/Lima",
                'America/Caracas' => "America/Caracas",
                'Canada/Atlantic' => "Canada/Atlantic Time (Canada)",
                'America/La_Paz' => "America/La Paz",
                'America/Santiago' => "America/Santiago",
                'Canada/Newfoundland' => "Canada/Newfoundland",
                'America/Buenos_Aires' => "America/Buenos_Aires",
                'Greenland' => "Greenland",
                'Atlantic/Stanley' => "Atlantic/Stanley",
                'Atlantic/Azores' => "Atlantic/Azores",
                'Atlantic/Cape_Verde' => "Atlantic/Cape Verde Is",
                'Africa/Casablanca' => "Africa/Casablanca",
                'Europe/Dublin' => "Europe/Dublin",
                'Europe/Lisbon' => "Europe/Lisbon",
                'Europe/London' => "Europe/London",
                'Africa/Monrovia' => "Africa/Monrovia",
                'Europe/Amsterdam' => "Europe/Amsterdam",
                'Europe/Belgrade' => "Europe/Belgrade",
                'Europe/Berlin' => "Europe/Berlin",
                'Europe/Bratislava' => "Europe/Bratislava",
                'Europe/Brussels' => "Europe/Brussels",
                'Europe/Budapest' => "Europe/Budapest",
                'Europe/Copenhagen' => "Europe/Copenhagen",
                'Europe/Ljubljana' => "Europe/Ljubljana",
                'Europe/Madrid' => "Europe/Madrid",
                'Europe/Paris' => "Europe/Paris",
                'Europe/Prague' => "Europe/Prague",
                'Europe/Rome' => "Europe/Rome",
                'Europe/Sarajevo' => "Europe/Sarajevo",
                'Europe/Skopje' => "Europe/Skopje",
                'Europe/Stockholm' => "Europe/Stockholm",
                'Europe/Vienna' => "Europe/Vienna",
                'Europe/Warsaw' => "Europe/Warsaw",
                'Europe/Zagreb' => "Europe/Zagreb",
                'Europe/Athens' => "Europe/Athens",
                'Europe/Bucharest' => "Europe/BucharestBucharest",
                'Africa/Cairo' => "Africa/Cairo",
                'Africa/Harare' => "Africa/Harare",
                'Europe/Helsinki' => "Europe/Helsinki",
                'Europe/Istanbul' => "Europe/Istanbul",
                'Asia/Jerusalem' => "Asia/Jerusalem",
                'Europe/Kiev' => "Europe/Kiev",
                'Europe/Minsk' => "Europe/Minsk",
                'Europe/Riga' => "Europe/Riga",
                'Europe/Sofia' => "Europe/Sofia",
                'Europe/Tallinn' => "Europe/Tallinn",
                'Europe/Vilnius' => "Europe/Vilnius",
                'Asia/Baghdad' => "Asia/Baghdad",
                'Asia/Kuwait' => "Asia/Kuwait",
                'Africa/Nairobi' => "Africa/Nairobi",
                'Asia/Riyadh' => "Asia/Riyadh",
                'Europe/Moscow' => "Europe/Moscow",
                'Asia/Tehran' => "Asia/Tehran",
                'Asia/Baku' => "Asia/Baku",
                'Europe/Volgograd' => "Europe/Volgograd",
                'Asia/Muscat' => "Asia/Muscat",
                'Asia/Tbilisi' => "Asia/Tbilisi",
                'Asia/Yerevan' => "Asia/Yerevan",
                'Asia/Kabul' => "Asia/Kabul",
                'Asia/Karachi' => "Asia/Karachi",
                'Asia/Tashkent' => "Asia/Tashkent",
                'Asia/Kolkata' => "Asia/Kolkata",
                'Asia/Kathmandu' => "Asia/Kathmandu",
                'Asia/Yekaterinburg' => "Asia/Yekaterinburg",
                'Asia/Almaty' => "Asia/Almaty",
                'Asia/Dhaka' => "Asia/Dhaka",
                'Asia/Novosibirsk' => "Asia/Novosibirsk",
                'Asia/Bangkok' => "Asia/Bangkok",
                'Asia/Jakarta' => "Asia/Jakarta",
                'Asia/Krasnoyarsk' => "Asia/Krasnoyarsk",
                'Asia/Chongqing' => "Asia/Chongqing",
                'Asia/Hong_Kong' => "Asia/Hong_Kong",
                'Asia/Kuala_Lumpur' => "Asia/Kuala Lumpur",
                'Australia/Perth' => "Australia/Perth",
                'Asia/Singapore' => "Asia/Singapore",
                'Asia/Taipei' => "Asia/Taipei",
                'Asia/Ulaanbaatar' => "Asia/Ulaan Baatar",
                'Asia/Urumqi' => "Asia/Urumqi",
                'Asia/Irkutsk' => "Asia/Irkutsk",
                'Asia/Seoul' => "Asia/Seoul",
                'Asia/Tokyo' => "Asia/Tokyo",
                'Australia/Adelaide' => "Australia/Adelaide",
                'Australia/Darwin' => "Australia/Darwin",
                'Asia/Yakutsk' => "Asia/Yakutsk",
                'Australia/Brisbane' => "Australia/Brisbane",
                'Australia/Canberra' => "Australia/Canberra",
                'Pacific/Guam' => "Pacific/Guam",
                'Australia/Hobart' => "Australia/Hobart",
                'Australia/Melbourne' => "Australia/Melbourne",
                'Pacific/Port_Moresby' => "Pacific/Port Moresby",
                'Australia/Sydney' => "Australia/Sydney",
                'Asia/Vladivostok' => "Asia/Vladivostok",
                'Asia/Magadan' => "Asia/Magadan",
                'Pacific/Auckland' => "Pacific/Auckland",
                'Pacific/Fiji' => "Pacific/Fiji",
            );

        if($timezone)
            return $timezones[$timezone];
        return $timezones;
    }
    public static function getCreditCardType($number)
    {
        $number=preg_replace('/[^\d]/','',$number);
        if (preg_match('/^3[47][0-9]{13}$/',$number))
        {
            return 'American Express';
        }
        elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',$number))
        {
            return 'Diners Club';
        }
        elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/',$number))
        {
            return 'Discover';
        }
        elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/',$number))
        {
            return 'JCB';
        }
        elseif (preg_match('/^5[1-5][0-9]{14}$/',$number))
        {
            return 'MasterCard';
        }
        elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/',$number))
        {
            return 'Visa';
        }
        else
        {
            return 'Unknown';
        }
    }
}
