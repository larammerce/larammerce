<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/16/18
 * Time: 8:48 PM
 */

namespace App\Utils\SMSManager\Drivers\Farapayamak;


use App\Utils\SMSManager\BaseDriver;
use App\Utils\SMSManager\ConfigProvider;
use App\Utils\SMSManager\Models\BaseSMSConfig;
use App\Utils\SMSManager\Models\TextMessage;
use Exception;
use Illuminate\Support\Facades\Log;

class Driver implements BaseDriver
{
    const DRIVER_ID = "farapayamak";

    public function sendSMS(TextMessage $text_message): bool
    {
        try {
            $text_message->data = array_merge($text_message->data, $text_message->mixed_data);
            $textContent = view('public.' . $text_message->template, $text_message->data)->render();
            $config = ConfigProvider::getConfig($this->getId());
            $curl_result = ConnectionFactory::create('/post/sendsms.ashx', $config)
                ->withData([
                    'text' => $textContent,
                    'to' => $text_message->receiver_number,
                    'from' => $config->number,
                    'username' => $config->username,
                    'password' => $config->password
                ])->post();
            if ($curl_result != '0')
                return true;
            return false;
        } catch (Exception $exception) {
            Log::error("SMS:Farapayamak:Send: can't load blade");
            return false;
        }
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
