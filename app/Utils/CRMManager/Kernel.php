<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/16/18
 * Time: 8:46 PM
 */

namespace App\Utils\CRMManager;

class Kernel {
    public static $drivers = [
        'sarv' => \App\Utils\CRMManager\Drivers\Sarv\Driver::class,
    ];
}
