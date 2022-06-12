<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/15/18
 * Time: 6:10 PM
 */

namespace App\Utils\FinancialManager;


use App\Utils\FinancialManager\Exceptions\FinancialDriverInvalidConfigurationException;

class Factory
{
    private static array $singleton = [];
    private static ?string $driver_name = null;

    /**
     * @throws FinancialDriverInvalidConfigurationException
     */
    private static function updateInstance(string $driver_name): void
    {
        if (count(static::$singleton) == 0 or
            static::$driver_name != $driver_name) {
            if (key_exists($driver_name, Kernel::$drivers)) {
                static::$singleton[$driver_name] = new Kernel::$drivers[$driver_name]();
                static::$driver_name = $driver_name;
            } else {
                throw new FinancialDriverInvalidConfigurationException("The financial driver with ID: $driver_name is not in " .
                    "financial kernel.");
            }
        }
    }

    /**
     * @throws FinancialDriverInvalidConfigurationException
     */
    public static function driver(string $driver_name = null): BaseDriver
    {
        if ($driver_name == null)
            $driver_name = Provider::getEnabledDriver();
        static::updateInstance($driver_name);
        return static::$singleton[$driver_name];
    }
}
