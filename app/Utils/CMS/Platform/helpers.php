<?php


use App\Utils\CMS\Platform\DetectService;
use App\Utils\CMS\Platform\OSType;

if (!function_exists('get_os')) {
    function get_os()
    {
        if(DetectService::isAndroid())
            return OSType::Android;
        else if (DetectService::isIOS())
            return OSType::IOS;
        return OSType::Other;
    }
}

if (!function_exists('is_desktop')) {
    function is_desktop()
    {

        return DetectService::isDesktop();
    }
}

if (!function_exists('is_mobile')) {
    function is_mobile()
    {
        return DetectService::isMobile();
    }
}

if (!function_exists('is_tablet')) {
    function is_tablet()
    {
       return DetectService::isTablet();
    }
}


if (!function_exists('is_ios')) {
    function is_ios()
    {
        return DetectService::isIOS();
    }
}


if (!function_exists('is_android')) {
    function is_android()
    {
        return DetectService::isAndroid();
    }
}