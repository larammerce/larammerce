<?php

namespace App\Utils\PaymentManager\Drivers\BehPardakht;

use App\Utils\PaymentManager\Models\BasePaymentConfig;

class Config extends BasePaymentConfig
{
    const LOGO_PATH = "/primary_data/payment_drivers/".Driver::DRIVER_ID."/logo.png";
    const HOST = 'https://bpm.shaparak.ir';
    const FORM_ACTION = '/pgwchannel/startpay.mellat';

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $tid;

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
