<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 9/10/17
 * Time: 3:03 PM
 */

namespace App\Utils\CMS;

use App\Utils\Common\RequestService;

/**
 * Class FormService
 * @package App\Utils\CMS
 */
class FormService
{
    public static function convertFormInputToKeys($inputs)
    {
        $keys = [];
        foreach ($inputs as $value)
            $keys[] = $value->text;
        RequestService::setAttr('fields', json_encode($keys));
    }
}
