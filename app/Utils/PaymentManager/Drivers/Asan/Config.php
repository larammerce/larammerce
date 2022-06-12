<?php

namespace App\Utils\PaymentManager\Drivers\Asan;

use App\Utils\PaymentManager\Models\BasePaymentConfig;

class Config extends BasePaymentConfig
{
    const LOGO_PATH = "/primary_data/payment_drivers/".Driver::DRIVER_ID."/logo.png";
    const API_HOST = "https://ipgrest.asanpardakht.ir";
    const FORM_ACTION = "https://asan.shaparak.ir";

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $mcid;

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $mid;

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
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="text")
     */
    public $iban;
}
