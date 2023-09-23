<?php

namespace App\Utils\FinancialManager\Drivers\HamkaranSystem;

use App\Utils\FinancialManager\Models\BaseFinancialConfig;

class Config extends BaseFinancialConfig
{

    /**
     * @rules(input_rule="required_if:is_enabled,true|string")
     * @data(input_type="text")
     */
    public $username;

    /**
     * @rules(input_rule="required_if:is_enabled,true|string")
     * @data(input_type="text")
     */
    public $password;

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
     * @data(input_type="text")
     */
    public $prefix;

    public function __construct()
    {
        parent::__construct();
        $this->username = "";
        $this->password = "";
        $this->host = "";
        $this->port = 443;
        $this->prefix = "";
    }
}
