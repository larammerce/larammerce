<?php

namespace App\Utils\CRMManager;

use App\Utils\CRMManager\Exceptions\CRMDriverNotConfiguredException;
use Illuminate\Support\Facades\Log;

class Provider
{
    public static function getAllDrivers(): array
    {
        return array_keys(Kernel::$drivers);
    }

    public static function getEnabledDriver(): string
    {
        $all_drivers_ids = self::getAllDrivers();
        foreach ($all_drivers_ids as $driver_id) {
            try {
                $driver_config = ConfigProvider::getConfig($driver_id);
                if ($driver_config->is_enabled)
                    return $driver_id;
            } catch (CRMDriverNotConfiguredException $e){
                  Log::error("CRM driver not configured.
                 ConfigProvider:getConfig:{$e->getMessage()}");
            }
        }
        return '';
    }

    public static function isEnabledDriver(string $driver_name): bool
    {
        return $driver_name === static::getEnabledDriver();
    }

    public static function hasDriver(string $driver_name): bool
    {
        return array_key_exists($driver_name, Kernel::$drivers);
    }
}
