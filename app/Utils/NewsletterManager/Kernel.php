<?php
namespace App\Utils\NewsletterManager;

class Kernel
{

    public static $drivers = [
        'mailerlite' => \App\Utils\NewsletterManager\Drivers\MailerLite\Driver::class,
    ];
}
