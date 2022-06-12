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
    public static function getFormProperties($request): array
    {
        $properties = [];
        foreach ($request->all() as $inputKey => $inputValue) {
            if (is_string($inputValue) and strlen($inputValue) != 0 and strpos($inputKey, 'property-key') !== false) {
                $propertyId = explode('-', $inputKey)[2];
                $properties[] = [
                    'key' => $inputValue,
                    'value' => $request->get('property-value-' . $propertyId),
                    'type' => $request->get('property-type-' . $propertyId),
                    'priority' => $request->get('property-priority-' . $propertyId),
                ];
            }
        }
        return $properties;
    }

    public static function getEncodedFormProperties($request): bool|string
    {
        return json_encode(self::getFormProperties($request));
    }

    public static function convertFormInputToKeys($inputs)
    {
        $keys = [];
        foreach ($inputs as $value)
            array_push($keys, $value->text);
        RequestService::setAttr('fields', json_encode($keys));
    }
}
