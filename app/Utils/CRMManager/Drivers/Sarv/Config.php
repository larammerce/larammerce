<?php

namespace App\Utils\CRMManager\Drivers\Sarv;

use App\Utils\CRMManager\Models\BaseCRMConfig;

class Config extends BaseCRMConfig {
    /**
     * @rules(input_rule="string")
     * @data(input_type="hidden")
     */
    public $token;

    /**
     * @rules(input_rule="string")
     * @data(input_type="hidden")
     */
    public $session_id;

    /**
     * @rules(input_rule="string")
     * @data(input_type="hidden")
     */
    public $token_created_at;

    /**
     * @rules(input_rule="number")
     * @data(input_type="number")
     */
    public $token_expiration_minutes;

    /**
     * @rules(input_rule="string")
     * @data(input_type="text")
     */
    public $utype;

    /**
     * @rules(input_rule="string")
     * @data(input_type="text")
     */
    public $username;

    /**
     * @rules(input_rule="string")
     * @data(input_type="text")
     */
    public $password;

    public function __construct() {
        parent::__construct();
        $this->host = 'app.sarvcrm.com';
        $this->port = 443;
        $this->ssl = true;
    }
}
