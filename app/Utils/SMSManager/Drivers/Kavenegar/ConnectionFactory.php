<?php

namespace App\Utils\SMSManager\Drivers\Kavenegar;

use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;

class ConnectionFactory
{
    public static function create(string $address, Config $config): Builder
    {
        $address = strpos($address, '/') == 0 ? $address : '/' . $address;
        $address = 'https://' . $config->host . '/v1/' .
            $config->token . $address;
        return Curl::to($address)->withHeader("content-type: application/json");
    }
}
