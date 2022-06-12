<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/11/19
 * Time: 8:25 PM
 */

namespace App\Utils\PaymentManager\Drivers\Asan;


use App\Utils\PaymentManager\Models\BasePaymentData;

class PaymentData extends BasePaymentData
{
    public $token;

    /**
     * PaymentData constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }


}