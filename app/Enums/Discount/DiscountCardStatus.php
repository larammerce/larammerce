<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/4/18
 * Time: 12:33 PM
 */

namespace App\Enums\Discount;


use App\Common\BaseEnum;

class DiscountCardStatus extends BaseEnum
{
    const VALID=0;
    const IS_USED=1;
    const NOT_FOR_YOU=2;
    const NOT_EXIST=3;
    const EXPIRED=4;
    const INACTIVE=5;
    const NOT_MATCH=6;
}
