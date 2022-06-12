<?php

namespace App\Utils\CMS\SiteMap;

class Provider
{
    public static function save()
    {
        foreach (Kernel::$drivers as $name => $class) {
            $driver = new $class();
            $driver->save();
        }
    }

    public static function get($driverName)
    {
        if (key_exists($driverName, Kernel::$drivers)) {
            $driver = new Kernel::$drivers[$driverName]();
            return $driver->generate();
        }
        return false;
    }
}