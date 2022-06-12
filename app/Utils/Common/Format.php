<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 9/2/17
 * Time: 3:07 PM
 */

namespace App\Utils\Common;


class Format
{
    private static $numbers = [
        '0' => '۰',
        '1' => '۱',
        '2' => '۲',
        '3' => '۳',
        '4' => '۴',
        '5' => '۵',
        '6' => '۶',
        '7' => '۷',
        '8' => '۸',
        '9' => '۹'
    ];

    /**
     * @param int $number
     * @param string $lang
     * @return string
     */
    public static function number($number, $lang = 'fa')
    {
        $number = (string)$number;
        $number = strrev($number);
        $number = str_split($number, 3);
        $number = join(',', $number);
        $number = strrev($number);

        if ($lang == 'fa')
            foreach (self::$numbers as $from => $to) {
                $number = str_replace($from, $to, $number);
            }

        return $number;
    }
}