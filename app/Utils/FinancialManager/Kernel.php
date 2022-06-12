<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/12/18
 * Time: 12:08 PM
 */

namespace App\Utils\FinancialManager;

class Kernel
{
    public static array $drivers = [
        'arpa' => \App\Utils\FinancialManager\Drivers\Arpa\Driver::class,
        'taraznegar' => \App\Utils\FinancialManager\Drivers\Taraznegar\Driver::class,
        'hamkaran' => \App\Utils\FinancialManager\Drivers\HamkaranSystem\Driver::class,
        'local' => \App\Utils\FinancialManager\Drivers\Local\Driver::class,
    ];
}
