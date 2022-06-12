<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/16/18
 * Time: 8:46 PM
 */

namespace App\Utils\SMSManager;


use App\Utils\SMSManager\Exceptions\SMSDriverInvalidConfigurationException;

class Factory
{
    private static $singleton = [];
    private static $driver_name = null;

    /**
     * @throws SMSDriverInvalidConfigurationException
     */
    private static function updateInstance(string $driver_name)
    {
        if (count(static::$singleton) == 0 or
            static::$driver_name != $driver_name) {
            if (key_exists($driver_name, Kernel::$drivers)) {
                static::$singleton[$driver_name] = new Kernel::$drivers[$driver_name]();
                static::$driver_name = $driver_name;
            } else {
                throw new SMSDriverInvalidConfigurationException("The sms driver with ID: $driver_name is not in " .
                    "sms kernel.");
            }
        }
    }

    /**
     * @throws SMSDriverInvalidConfigurationException
     */
    public static function driver(string $driver_name = null): BaseDriver
    {
        if ($driver_name == null)
            $driver_name = Provider::getEnabledDriver();
        static::updateInstance($driver_name);
        return static::$singleton[$driver_name];
    }
}
