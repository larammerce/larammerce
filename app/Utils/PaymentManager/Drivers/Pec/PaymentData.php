<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/11/19
 * Time: 8:25 PM
 */

namespace App\Utils\PaymentManager\Drivers\Pec;


use App\Utils\PaymentManager\Models\BasePaymentData;

class PaymentData extends BasePaymentData
{
    public $token;
    public $status;

    /**
     * PaymentData constructor.
     * @param $token
     * @param $status
     */
    public function __construct($token, $status)
    {
        $this->token = $token;
        $this->status = $status;
    }


}