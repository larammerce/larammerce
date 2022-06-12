<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/12/17
 * Time: 3:37 PM
 */

namespace App\Models\Enums;


use App\Utils\Common\BaseEnum;

class Gender extends BaseEnum
{
    const MALE = 0;
    const FEMALE = 1;
    const NONE = 2;
}