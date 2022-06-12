<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/15/18
 * Time: 6:10 PM
 */

namespace App\Utils\PaymentManager;


use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;

class Factory
{
    private static $singleton = [];

    /**
     * @throws PaymentInvalidDriverException
     */
    private static function updateInstance(string $driver_name = null)
    {
        if (static::$singleton == null or
            !key_exists($driver_name, static::$singleton)) {
            if (key_exists($driver_name, Kernel::$drivers)) {
                static::$singleton[$driver_name] = new Kernel::$drivers[$driver_name]();
            } else {
                throw new PaymentInvalidDriverException("The payment driver with ID: $driver_name is not in " .
                    "payment kernel.");
            }
        }
    }

    /**
     * @throws PaymentInvalidDriverException
     */
    public static function driver(string $driver_name = null): AbstractDriver
    {
        if ($driver_name == null)
            $driver_name = Provider::getDefaultDriver();
        static::updateInstance($driver_name);
        return static::$singleton[$driver_name];
    }
}
