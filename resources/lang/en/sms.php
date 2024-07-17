<?php

return [
    "drivers" => [
        "file" => "Save to file (do not send)",
        "farapayamak" => "SMS short message system",
        "kavenegar" => "Kaveh Nagar short message service",
    ],

    "config" => [
        "is_enabled" => "Activation",
        "username" => "User name",
        "password" => "Password",
        "token" => "Token",
        "host" => "Address host",
        "port" => "Port",
        "number" => "Number",
        "flash_support" => "Pop-up display and no saving in the recipient's device",
        "can_send_sms_for_invoice_submit" => "Send an SMS to place an order",
        "can_send_sms_for_invoice_paid" => "Send SMS for paid",
        "can_send_sms_for_invoice_cancel" => "Send an SMS to cancel the order",
        "can_send_sms_for_invoice_sending" => "Send SMS for sending",
        "can_send_sms_for_invoice_sent" => "Send SMS to sent",
        "can_send_sms_for_invoice_delivered" => "Send SMS for delivered",
        "can_send_sms_for_invoice_survey" => "Send SMS for survey",
    ]
];
