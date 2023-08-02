<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 1/22/19
 * Time: 2:11 PM
 */

namespace App\Utils\CMS\CustomerAuth;


use App\Common\BaseEnum;

class AuthType extends BaseEnum
{
    const MOBILE="mobile";
    const EMAIL="email";

    public static function fix($type, $defaultType="mobile"){
        return in_array($type, static::values()) ? $type : $defaultType;
    }
}
