<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 9/10/17
 * Time: 3:03 PM
 */

namespace App\Helpers;

/**
 * Class FormHelper
 * @package App\Utils\CMS
 */
class FormHelper
{
    public static function convertFormInputToKeys($inputs): void {
        $keys = [];
        foreach ($inputs as $value)
            $keys[] = $value->text;
        RequestHelper::setAttr('fields', json_encode($keys));
    }
}
