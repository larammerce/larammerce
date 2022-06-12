<?php

namespace App\Utils\PaymentManager\Drivers\Pec;

use App\Utils\PaymentManager\Models\BasePaymentConfig;

class Config extends BasePaymentConfig
{
    const LOGO_PATH = "/primary_data/payment_drivers/".Driver::DRIVER_ID."/logo.png";
    const HOST = 'https://pec.shaparak.ir';
    const FORM_ACTION = 'https://pec.shaparak.ir/NewIPG/';

    /**
     * @rules(input_rule="required_if:is_enabled,true|string")
     * @data(input_type="text")
     */
    public $pin;

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $tid;
}
