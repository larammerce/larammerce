<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/25/18
 * Time: 4:03 PM
 */

return [
    "drivers" => [
        "sep" => "Electronic payment of Saman Kish",
        "local" => "Payment with virtual wallet",
        "mabna" => "Arya card base",
        "pep" => "Pasargad electronic payment",
        "sepehrpay" => "Sepehr electronic payment",
        "pec" => "Persian e-commerce",
        "asan" => "Easy Persian payment",
        "behpardakht" => "To pay the nation",
        "zarinpal" => "Zarin Pal"
    ],
    "status" => [
        'Awaiting Payment',
        'Needs to be checked',
        'successful',
        'Cash payment',
        'Unsuccessful',
        'Canceled',
        'Refund',
    ],
    "local" => [
        "invalid_request" => "Invalid request for payment",
        "not_enough_credit" => "Unfortunately, your credit is not enough to pay",
    ],
    "config" => [
        "tid" => "Terminal ID",
        "mid" => "Business ID",
        "mcid" => "Commercial certificate identification number",
        "merchant_id" => "Port ID",
        "username" => "user name",
        "password" => "password",
        "iban" => "Shaba-IBAN",
        "private_key" => "Private key",
        "pin" => "pin",
        "is_enabled"=> "Port activation",
        "is_default" => "Default port"
    ]
];
