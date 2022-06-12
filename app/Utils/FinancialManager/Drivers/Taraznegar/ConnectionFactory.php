<?php namespace App\Utils\FinancialManager\Drivers\Taraznegar;

use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;

class ConnectionFactory
{
    /**
     * @param string $address
     * @return Builder
     */
    public static function create($address, Config $config){
        $address = strpos($address, '/') == 0 ? $address : '/'.$address;
        $address = $config->get_base_url().$address;
        return Curl::to($address)->
            withHeader("content-type: application/json");

    }
}
