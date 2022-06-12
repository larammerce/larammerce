<?php

return [

    "aps" => [
        /*
         * A valid PEM certificate generated from Apple Push Service certificate
         */
        "certificate" 	=> storage_path('app')."/aps.pem",

        /*
         * Password used to generate a certificate
         */
        "passPhrase"  	=> ""
    ],

    "gcm" => [
        /*
         * Google GCM api key
         * You can retrieve your key in Google Developer Console
         */
        "apiKey"      	=> "",
    ]

];