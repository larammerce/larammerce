<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/12/18
 * Time: 2:44 PM
 */

namespace App\Utils\FinancialManager\Drivers\Arpa;


use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;

class ConnectionFactory
{
    public static function create(string $address, Config $config): Builder
    {
        $address = strpos($address, '/') == 0 ? $address : '/' . $address;
        $address = 'http://' . $config->host . ':' . $config->port . $address;
        $curl = Curl::to($address)->withHeader("content-type: application/json")
            ->enableDebug(storage_path("logs/arpa_curl.log"));
        return ($config->token == null or strlen($config->token) === 0) ? $curl :
            $curl->withHeader("authorization: Bearer {$config->token}");
    }
}
