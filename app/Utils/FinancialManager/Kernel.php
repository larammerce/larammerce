<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/12/18
 * Time: 12:08 PM
 */

namespace App\Utils\FinancialManager;

class Kernel {
    public static array $drivers = [
        Drivers\Arpa\Driver::DRIVER_ID => Drivers\Arpa\Driver::class,
        Drivers\Taraznegar\Driver::DRIVER_ID => Drivers\Taraznegar\Driver::class,
        Drivers\HamkaranSystem\Driver::DRIVER_ID => Drivers\HamkaranSystem\Driver::class,
        Drivers\Darik\Driver::DRIVER_ID => Drivers\Darik\Driver::class,
        Drivers\Local\Driver::DRIVER_ID => Drivers\Local\Driver::class,
    ];
}
