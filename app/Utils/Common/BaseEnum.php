<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/12/17
 * Time: 5:00 PM
 */

namespace App\Utils\Common;


use ReflectionClass;

abstract class BaseEnum
{
    /**
     * @return string[]
     */
    public static function toMap()
    {
        $oClass = new ReflectionClass(get_called_class());
        return $oClass->getConstants();
    }

    public static function keys()
    {
        return array_keys(static::toMap());
    }

    public static function values()
    {
        return array_values(static::toMap());
    }

    public static function stringValues()
    {
        return implode(",", static::values());
    }
}