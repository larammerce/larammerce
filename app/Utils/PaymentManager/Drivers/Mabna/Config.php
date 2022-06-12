<?php

namespace App\Utils\PaymentManager\Drivers\Mabna;

use App\Utils\PaymentManager\Models\BasePaymentConfig;

class Config extends BasePaymentConfig
{
    const LOGO_PATH = "/primary_data/payment_drivers/".Driver::DRIVER_ID."/logo.png";
    const API_HOST = 'https://mabna.shaparak.ir:8081';
    const FORM_ACTION = 'https://mabna.shaparak.ir:8080/Pay';

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $mid;

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $tid;
}
