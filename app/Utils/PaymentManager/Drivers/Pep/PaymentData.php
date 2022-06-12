<?php
/**
 * Created by PhpStorm.
 * User: amirhosein
 * Date: 1/27/19
 * Time: 2:04 PM
 */

namespace App\Utils\PaymentManager\Drivers\Pep;


class PaymentData
{
    public $sign;

    /**
     * PaymentData constructor.
     * @param $sign
     */
    public function __construct($sign)
    {
        $this->sign = $sign;
    }


}