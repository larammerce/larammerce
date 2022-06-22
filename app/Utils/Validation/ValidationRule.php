<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 7/29/2018 AD
 * Time: 13:49
 */

namespace App\Utils\Validation;


class ValidationRule
{
    /**
     * validate persian alphabet and space
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return boolean
     */
    public function alpha($attribute, $value, $parameters, $validator)
    {
        if (app()->getLocale() == 'fa') {
            $pattern = "/^[\x{600}-\x{6FF}\x{200c}\x{064b}\x{064d}\x{064c}\x{064e}\x{064f}\x{0650}\x{0651}\s]+$/u";
            return (bool)preg_match($pattern, $value);
        }
        return true;
    }

    /**
     * validate persian national-code
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     */
    public function nationalCode($attribute, $value, $parameters, $validator)
    {
        if (strlen($value) != 10)
            return false;
        $count_values = array_count_values(str_split($value));
        if (count(is_countable($count_values)?$count_values :[]) == 1)
            return false;
        $chars = str_split($value);
        $chars = array_reverse($chars);
        $sumResult = 0;
        foreach ($chars as $index => $char) {
            if ($index > 0)
                $sumResult += (intval($char) * ($index + 1));
        }
        $sumResult = $sumResult % 11;
        if ($sumResult < 2) {
            return intval($chars[0]) == $sumResult;
        } else {
            return intval($chars[0]) == 11 - $sumResult;
        }
    }

    /**
     * validate mobile number
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return boolean
     */
    public function mobileNumber($attribute, $value, $parameters, $validator)
    {
        $pattern = "/^09[0-9]{9}$/u";
        return (bool)preg_match($pattern, $value);
    }
}
