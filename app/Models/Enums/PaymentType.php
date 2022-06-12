<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/12/17
 * Time: 3:41 PM
 */

namespace App\Models\Enums;


use App\Utils\Common\BaseEnum;

class PaymentType extends BaseEnum
{
    const ONLINE = 0;
    const CASH = 1;
    const CREDIT = 2;
}