<?php
/**
 * Created by PhpStorm.
 * User: amirhosein
 * Date: 1/23/19
 * Time: 3:47 PM
 */
namespace App\Utils\SMSManager\Drivers\Kavenegar;

use App\Utils\SMSManager\BaseDriver;
use App\Utils\SMSManager\ConfigProvider;
use App\Utils\SMSManager\Models\BaseSMSConfig;
use App\Utils\SMSManager\Models\TextMessage;
use Illuminate\Support\Facades\Log;

class Driver implements BaseDriver
{
    const DRIVER_ID = "kavenegar";

    public function sendSMS(TextMessage $text_message): bool
    {
        $data = [
            "template" => $text_message->template,
            "receptor" => $text_message->receiver_number
        ];

        if (isset($text_message->data) and sizeof($text_message->data) > 0) {
            foreach (array_values($text_message->data) as $index => $dataVal) {
                $tokenId = $index + 1;
                $dataKey = $tokenId === 1 ? "token" : "token{$tokenId}";
                $data[$dataKey] = trim($dataVal);
            }
        } else
            $data['token'] = '.';

        foreach (array_values($text_message->mixed_data) as $index => $dataVal){
            $tokenId = ($index + 1) * 10;
            $dataKey = "token{$tokenId}";
            $data[$dataKey] = trim($dataVal);
        }
        $config = ConfigProvider::getConfig($this->getId());
        $result = ConnectionFactory::create('/verify/lookup.json', $config)->withData($data)->asJson()->get();

        if(isset($result->return) and
            isset($result->return->status) and
            $result->return->status == 200)
            return true;
        else {
            Log::error("SMS:Kavenegar:Send:". json_encode($result));
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
