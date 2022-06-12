<?php

namespace App\Utils\CMS\SiteMap;

class Kernel
{
    public static $drivers = [
        'html' => \App\Utils\CMS\SiteMap\Drivers\Html::class,
        'xml' => \App\Utils\CMS\SiteMap\Drivers\Xml::class
    ];
}