<?php

namespace App\Utils\PaymentManager\Drivers\Sep;

use App\Utils\PaymentManager\Models\BasePaymentConfig;

class Config extends BasePaymentConfig
{
    const LOGO_PATH = "/primary_data/payment_drivers/".Driver::DRIVER_ID."/logo.png";
    const HOST = 'https://sep.shaparak.ir';
    const INIT_PAYMENT = '/payments/initpayment.asmx';
    const INIT_REFERENCE = '/payments/referencepayment.asmx';
    const FORM = '/payment.aspx';

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

}
