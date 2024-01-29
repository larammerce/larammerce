<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/25/18
 * Time: 4:03 PM
 */

return [
    "drivers" => [
        "sep" => "Saman Electronic Payment ",
        "local" => "Payment with virtual wallet",
        "mabna" => "Mabna Card Aria",
        "pep" => "Pasargad Electronic Payment",
        "sepehrpay" => "Sepehr Electronic Payment",
        "pec" => "Persian E-commerce",
        "asan" => "Asan Pardakht",
        "behpardakht" => "Behpardakht Mellat",
        "zarinpal" => "Zarin Pal"
    ],
    "status" => [
        'Awaiting payment',
        'Needs to be checked',
        'Successful',
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
        "username" => "Username",
        "password" => "Password",
        "iban" => "Shaba-IBAN",
        "private_key" => "Private key",
        "pin" => "PIN",
        "is_enabled"=> "Port activation",
        "is_default" => "Default port"
    ]
];
