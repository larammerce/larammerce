<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/16/18
 * Time: 8:48 PM
 */

namespace App\Utils\SMSManager\Drivers\File;


use App\Utils\SMSManager\BaseDriver;
use App\Utils\SMSManager\Models\BaseSMSConfig;
use App\Utils\SMSManager\Models\TextMessage;
use Illuminate\Support\Facades\Log;

class Driver implements BaseDriver
{
    const DRIVER_ID = "file";

    public function sendSMS(TextMessage $text_message): bool
    {
        Log::info("SMS Driver file: send: template {$text_message->template} number: {$text_message->receiver_number}".
            json_encode($text_message->data));
        return true;
    }

    public function getId(): string
    {
        return self::DRIVER_ID;
    }

    public function getDefaultConfig(): BaseSMSConfig
    {
        return new Config();
    }
}
