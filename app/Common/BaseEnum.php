<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/12/17
 * Time: 5:00 PM
 */

namespace App\Common;


use ReflectionClass;

abstract class BaseEnum
{
    /**
     * @return string[]
     */
    public static function toMap(): array
    {
        $oClass = new ReflectionClass(get_called_class());
        return $oClass->getConstants();
    }

    public static function keys(): array
    {
        return array_keys(static::toMap());
    }

    public static function values(): array
    {
        return array_values(static::toMap());
    }

    public static function stringValues(): string
    {
        return implode(",", static::values());
    }
}
