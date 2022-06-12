<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/16/18
 * Time: 8:47 PM
 */

namespace App\Utils\SMSManager\Drivers\Farapayamak;


use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;

class ConnectionFactory
{
    public static function create(string $address, Config $config): Builder
    {
        $address = strpos($address, '/') == 0 ? $address : '/' . $address;
        $address = 'http://' . $config->host . ':' .
            $config->port . $address;
        return Curl::to($address);
    }
}
