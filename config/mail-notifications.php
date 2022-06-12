<?php

return [
    'website_owner_mail' => env('MAIL_WEBSITE_OWNER', ''),
    'invoices' => [
        'new_invoice_payment'=> env('MAIL_NEW_INVOICE_PAYMENT_NOTIFICATION'),
        'new_invoice'=> env('MAIL_NEW_INVOICE_NOTIFICATION'),
        'related_mail'=> env('RELATED_MAIL_RECEIVE_ORDER_NOTIFICATION')
    ],
    'forms' => [
        'new_form'=> env('MAIL_RECEIVE_FORMS_NOTIFICATION'),
        'related_mail'=> env('RELATED_MAIL_RECEIVE_NOTIFICATION_FORMS')
    ]

];