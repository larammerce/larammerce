<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/24/18
 * Time: 5:33 PM
 */

namespace App\Utils\PaymentManager;

use App\Utils\PaymentManager\Drivers\{
    Mabna, Asan, Pep, Sep, Sepehrpay, Pec, BehPardakht
};

class Kernel
{
    public static $sessionKey = "payment-manager";

    public static $drivers = [
        Sep\Driver::DRIVER_ID => Sep\Driver::class,
        Mabna\Driver::DRIVER_ID => Mabna\Driver::class,
        Asan\Driver::DRIVER_ID => Asan\Driver::class,
        Pep\Driver::DRIVER_ID => Pep\Driver::class,
        Sepehrpay\Driver::DRIVER_ID => Sepehrpay\Driver::class,
        Pec\Driver::DRIVER_ID => Pec\Driver::class,
        BehPardakht\Driver::DRIVER_ID => BehPardakht\Driver::class,
    ];
}
