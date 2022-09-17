<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/19/17
 * Time: 10:21 AM
 */

namespace App\Utils\Common;

use App\Utils\Jalali\JDate;

class TimeService
{
    const NULL_DATE = "0000-00-00 00:00:00";
    private static $dateFormat = "%d, %B %Y";
    private static $formalDateFormat = "%Y/%m/%d";
    private static $timeFormat = "%H:%M:%S";

    public static function getCurrentDate()
    {
        return JDate::forge()->format(self::$dateFormat);
    }

    public static function getCurrentFormalDate()
    {
        return JDate::forge()->format(self::$formalDateFormat);
    }

    public static function getDateFrom($date)
    {
        if ($date == self::NULL_DATE)
            return '-';
        return JDate::forge($date)->format(self::$dateFormat);
    }

    public static function getFormalDateFrom($date)
    {
        if ($date == self::NULL_DATE)
            return '-';
        return JDate::forge($date)->format(self::$formalDateFormat);
    }

    public static function getTimeFrom($date)
    {
        if ($date == self::NULL_DATE)
            return '-';
        return JDate::forge($date)->format(self::$timeFormat);
    }

    public static function getDateTimeFrom($date)
    {
        if ($date == self::NULL_DATE)
            return '-';
        return JDate::forge($date)->format(self::$formalDateFormat . " " . self::$timeFormat);
    }

    public static function getCustomFormatFrom($date, $format)
    {
        if ($date == self::NULL_DATE)
            return '-';
        return JDate::forge($date)->format($format);
    }
}
