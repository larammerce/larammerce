<?php

namespace App\Utils\PaymentManager\Drivers\Zarinpal;

use App\Utils\PaymentManager\Models\BasePaymentConfig;

class Config extends BasePaymentConfig {

    const LOGO_PATH = "/primary_data/payment_drivers/".Driver::DRIVER_ID."/logo.png";

    const API_HOST = "https://api.zarinpal.com";

    const FORM_ACTION = "https://www.zarinpal.com/pg/StartPay/";
    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="text")
     */
    public $merchant_id;
}