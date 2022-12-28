<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/12/18
 * Time: 2:44 PM
 */

namespace App\Utils\FinancialManager\Drivers\Darik;

use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;

class ConnectionFactory {
    public static function create(Config $config): Builder {
        $curl = Curl::to($config->graphql_address)
            ->withHeader("content-type: application/json")
            ->withHeader("accept: application/json")
            ->enableDebug(storage_path("logs/darik_curl.log"));
        return ($config->token == null or strlen($config->token) === 0) ? $curl :
            $curl->withHeader("authorization: Bearer {$config->token}");
    }
}
