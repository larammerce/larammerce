<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/9/17
 * Time: 9:17 PM
 */

namespace App\Helpers;


class AdminRequestHelper
{
    private static string $ADMIN_AREA_KEY = 'in_admin_area';

    public static function setInAdminArea($request = null)
    {
        $searchResult = array_search('admin', explode("/", $request->server('REQUEST_URI')));
        $result = ($searchResult !== false and $searchResult < 2);
        RequestHelper::setAttr(self::getAdminAreaKey(), $result, $request);
        //TODO: remove the in_admin_area if there's no admin in first two section.
    }

    public static function isInAdminArea($request = null)
    {
        return RequestHelper::getAttr(self::getAdminAreaKey(), $request);
    }

    public static function getAdminAreaKey(): string
    {
        return self::$ADMIN_AREA_KEY;
    }
}
