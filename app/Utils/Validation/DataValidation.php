<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/31/16
 * Time: 12:30 AM
 */

namespace App\Utils\Validation;


use DateTime;

class DataValidation
{
    public static function validateDate($date)
    {
        if(!is_string($date)){
            return false;
        }
        $d = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $d && $d->format('Y-m-d H:i:s') === $date;
    }

    public static function urlEncode($string){
        $string = strtolower($string);
        $replacements = array(
            '!' => '',
            '*' => '',
            '"' => '',
            "'" => "",
            "(" => "",
            ")" => "",
            ";" => "",
            ":" => "",
            "@" => "",
            "&" => "",
            "=" => "",
            "+" => "",
            "$" => "",
            "," => "",
            "/" => "",
            "?" => "",
            "%" => "",
            "#" => "",
            "[" => "",
            "]" => "",
            " " => "-");
        foreach($replacements as $from => $to) {
            $string = trim(str_replace($from, $to, $string));
        }
        return $string ;
    }
}