<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/7/18
 * Time: 1:48 PM
 */

namespace App\Models\Enums;


use App\Utils\Common\BaseEnum;

class PSAttrKeyShowType extends BaseEnum
{
    const NORMAL = 0;
    const DETAILS = 1;
    const HIDDEN = 9;
}