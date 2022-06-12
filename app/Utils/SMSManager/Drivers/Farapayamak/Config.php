<?php

namespace App\Utils\SMSManager\Drivers\Farapayamak;

use App\Utils\SMSManager\Models\BaseSMSConfig;

class Config extends BaseSMSConfig
{
    public function __construct()
    {
        parent::__construct();
        $this->host = 'api.payamak-panel.com';
        $this->port = 80;
    }

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $number;

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
}
