<?php

namespace App\Utils\CRMManager\Drivers\Sarv;

use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;

class ConnectionFactory {
    public static function createV5(string $address, Config $config): Builder {
        $address = strpos($address, '/') == 0 ? $address : '/' . $address;
        $address = ($config->ssl ? "https" : "http") . "://" . $config->host . ":" . $config->port . $address;
        return Curl::to($address)
            ->withBearer($config->token)
            ->withHeader("content-type: application/json");
    }

    public static function createV4(string $address, Config $config): Builder {
        $address = strpos($address, '/') == 0 ? $address : '/' . $address;
        $address = ($config->ssl ? "https" : "http") . "://" . $config->host . ":" . $config->port . $address;
        return Curl::to($address)
            ->withHeader("content-type: application/json");
    }
}
