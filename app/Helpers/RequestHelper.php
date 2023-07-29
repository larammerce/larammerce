<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/9/17
 * Time: 9:15 PM
 */

namespace App\Helpers;

class RequestHelper
{
    const TEXT_ATTRIBUTE="string";
    const FILE_ATTRIBUTE="Illuminate\\Http\\UploadedFile";

    public static function setAttr($key, $value, $request=null){
        $request = self::getRequest($request);
        $request->merge([$key => $value]);
    }

    public static function getAttr($key, $request=null){
        $request = self::getRequest($request);
        return $request->get($key);
    }

    public static function hasAttr($key, $request=null){
        $request = self::getRequest($request);
        return $request->has($key);
    }

    public static function isRequestAjax($request=null){
        $request = self::getRequest($request);
        return $request->ajax() || $request->wantsJson();
    }

    public static function getType($value){
        $valueType = gettype($value);
        return $valueType === "object" ? get_class($value) : $valueType;
    }

    private static function getRequest($request){
        return $request==null ? request() : $request;
    }
}