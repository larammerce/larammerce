<?php

namespace App\Utils\ShipmentService;

class Factory
{
    /**
     * @param $driverName
     * @return bool | BaseDriver
     */
    public static function driver($driverName)
    {
        if (key_exists($driverName, Kernel::$drivers)) {
            $driver = new Kernel::$drivers[$driverName]();
            return $driver;
        }
        return false;
    }
}