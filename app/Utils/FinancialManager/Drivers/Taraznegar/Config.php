<?php

namespace App\Utils\FinancialManager\Drivers\Taraznegar;

use App\Utils\FinancialManager\Models\BaseFinancialConfig;

class Config extends BaseFinancialConfig
{
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
        $this->prefix = "";
    }

    public function get_base_url(): string
    {
        return "http://" . $this->host . ":" .
        $this->port . "/" . $this->prefix . "/api";
    }
}
