<?php
/**
 * Created by PhpStorm.
 * User: amirhosein
 * Date: 8/2/18
 * Time: 11:48 AM
 */

namespace App\Helpers;

use App\Jobs\SendSms;


class SMSHelper
{

    /**
     * @param $template
     * @param $phone_number
     * @param array $data
     * @param array $mixed_data
     */
    public static function send($template, $phone_number, $data = [], $mixed_data = [])
    {
        $job = new sendSMS($template, $phone_number, $data, $mixed_data);
        dispatch($job);
    }
}
