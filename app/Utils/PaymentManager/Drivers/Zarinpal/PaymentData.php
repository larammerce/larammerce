<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/11/19
 * Time: 8:25 PM
 */

namespace App\Utils\PaymentManager\Drivers\Zarinpal;

use App\Utils\PaymentManager\Models\BasePaymentData;

class PaymentData extends BasePaymentData
{
    public $authority;
    public $code;
    public $message;

    /**
     * PaymentData constructor.
     * @param $token
     */
    public function __construct($authority, $code, $message)
    {
        $this->authority = $authority;
        $this->code = $code;
        $this->message = $message;
    }


}