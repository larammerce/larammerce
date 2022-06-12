<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/16/18
 * Time: 8:46 PM
 */

namespace App\Utils\SMSManager;

class Kernel
{
    public static $drivers = [
        'farapayamak' => \App\Utils\SMSManager\Drivers\Farapayamak\Driver::class,
        'file' => \App\Utils\SMSManager\Drivers\File\Driver::class,
        'kavenegar' => \App\Utils\SMSManager\Drivers\Kavenegar\Driver::class,
    ];
}
