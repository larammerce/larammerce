<?php

return [


    'default' => env('NEWSLETTER_MAIL_DRIVER', ''),

    /*
   |--------------------------------------------------------------------------
   | SMS Drivers
   |--------------------------------------------------------------------------
   | Here are each of the sms drivers setup for your application.
   | All jobs work using sync, redis Queue Drivers
   */

    'drivers' => [

        'file' => [],

        'mailerlite' => [
            'username' => env('NEWSLETTER_MAIL_USERNAME'),
            'password' => env('NEWSLETTER_MAIL_PASSWORD'),
            'api_key' => env('NEWSLETTER_API_KEY'),
            'group_id' => env('NEWSLETTER_GROUP_ID'),
            'host' => 'https://api.mailerlite.com/api/v2/',
            'port' => 80,
            'flash-support' => false,
        ],

    ],


];