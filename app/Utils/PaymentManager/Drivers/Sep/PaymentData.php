<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/26/18
 * Time: 5:18 PM
 */

namespace App\Utils\PaymentManager\Drivers\Sep;


use App\Utils\PaymentManager\Models\BasePaymentData;

class PaymentData extends BasePaymentData
{
    public $token;

    /**
     * PaymentData constructor.
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

}