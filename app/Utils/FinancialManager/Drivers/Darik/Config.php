<?php

namespace App\Utils\FinancialManager\Drivers\Darik;

use App\Utils\FinancialManager\Models\BaseFinancialConfig;

class Config extends BaseFinancialConfig {

    /**
     * @rules(input_rule="required_if:is_enabled,true|string")
     * @data(input_type="text")
     */
    public $token;

    /**
     * @rules(input_rule="required_if:is_enabled,true|string")
     * @data(input_type="text")
     */
    public $graphql_address;
}