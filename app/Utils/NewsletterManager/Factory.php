<?php

namespace App\Utils\NewsletterManager;


class Factory
{
    private static $singleton = null;
    private static $driverName = null;

    private static function updateInstance()
    {
        $driverName = env('NEWSLETTER_DRIVER', 'default');
        if (static::$singleton == null or
            static::$driverName != $driverName) {
            if (key_exists($driverName, Kernel::$drivers)) {
                static::$singleton = new Kernel::$drivers[$driverName]();
                static::$driverName = $driverName;
            } else {
                static::$singleton = null;
                static::$driverName = null;
            }
        }
    }

    /**
     * @return BaseDriver
     */
    public static function driver()
    {
        static::updateInstance();
        return static::$singleton;
    }
}