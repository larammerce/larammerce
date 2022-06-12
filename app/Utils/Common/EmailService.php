<?php
/**
 * Created by PhpStorm.
 * User: a.morteza
 * Date: 3/9/19
 * Time: 5:17 PM
 */

namespace App\Utils\Common;


use App\Jobs\SendEmail;

class EmailService
{
    public static function send($data, $template, $email, $name, $subject){
        $job = new SendEmail($data,
            $template,
            $email,
            $name,
            $subject
        );
        dispatch($job);
    }
}