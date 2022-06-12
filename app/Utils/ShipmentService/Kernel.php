<?php

namespace App\Utils\ShipmentService;

class Kernel
{
    public static $drivers = [
        'none' => Drivers\None::class,
        'express' => Drivers\Express::class,
        'post' => Drivers\Post::class,
    ];
}