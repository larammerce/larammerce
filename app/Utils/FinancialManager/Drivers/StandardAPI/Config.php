<?php

namespace App\Utils\FinancialManager\Drivers\StandardAPI;

use App\Utils\FinancialManager\Models\BaseFinancialConfig;

class Config extends BaseFinancialConfig
{

    /**
     * @rules(input_rule="required_if:is_enabled,true|string")
     * @data(input_type="text")
     */
    public $token;

    /**
     * @rules(input_rule="required_if:is_enabled,true|string")
     * @data(input_type="text")
     */
    public $host;

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $port;

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $exchange_rage;

    /**
     * @rules(input_rule="required_if:is_enabled,true|array")
     * @data(input_type="custom")
     */
    public $formula;
}
