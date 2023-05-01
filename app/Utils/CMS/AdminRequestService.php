<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/9/17
 * Time: 9:17 PM
 */

namespace App\Utils\CMS;


use App\Utils\Common\RequestService;

class AdminRequestService
{
    private static string $ADMIN_AREA_KEY = 'in_admin_area';

    public static function setInAdminArea($request = null)
    {
        $searchResult = array_search('admin', explode("/", $request->server('REQUEST_URI')));
        $result = ($searchResult !== false and $searchResult < 2);
        RequestService::setAttr(self::getAdminAreaKey(), $result, $request);
        //TODO: remove the in_admin_area if there's no admin in first two section.
    }

    public static function isInAdminArea($request = null)
    {
        return RequestService::getAttr(self::getAdminAreaKey(), $request);
    }

    public static function getAdminAreaKey(): string
    {
        return self::$ADMIN_AREA_KEY;
    }
}
