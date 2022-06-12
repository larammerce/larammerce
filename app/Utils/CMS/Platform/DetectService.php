<?php


namespace App\Utils\CMS\Platform;

class DetectService
{
    /**
     * @return bool
     */
    public static function isDesktop()
    {
        return !app('mobile-detect')->isMobile();
    }

    /**
     * @return bool
     */
    public static function isMobile()
    {
        return app('mobile-detect')->isMobile() && !app('mobile-detect')->isTablet();
    }

    /**
     * @return bool
     */
    public static function isTablet()
    {
        return app('mobile-detect')->isTablet();
    }

    /**
     * @return bool
     */
    public static function isIOS()
    {
        return app('mobile-detect')->is('iOS');
    }

    /**
     * @return bool
     */
    public static function isAndroid()
    {
        return app('mobile-detect')->is('Android');
    }

    public static function getBrowser()
    {
        // TODO: Implement getBrowser() method.
    }
}