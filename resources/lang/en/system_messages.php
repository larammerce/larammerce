<?php

return [
    'user' => [
        'password_changed' => 'Password changed successfully. ',
        'wrong_password' => 'Password entered is incorrect.',
        'not_found' => 'User was not found.',
        'invalid_token' => 'The link is invalid, please try again.',
        'repeated_request' => 'Password recovery email already sent to you, please check your email. (Remaining time for next request:: remaining_minutes minutes and: remaining_seconds seconds)',
        'reset_password_email_sent' => 'Password recovery email sent.',
        'register_done' => 'Your registration was successful.',
        'register_email_sent' => 'Registration completed successfully, please check your email.',
        'confirm_email_sent' => 'Your account verification email has been sent to you.',
        'register_confirm_error' => 'Your registration has encountered problems, please try again later.',
        'account_registered' => 'Your account was created successfully.',
        'account_activation_error' => 'There was a problem activating your account, please try again later.',
        'account_activation_success' => 'Your account activation completed successfully.',
        'email_confirm_error' => 'There was a problem verifying your email address, please try again.',
        'email_confirmed' => 'Your email account has been successfully verified.',
        'profile_updated' => 'Changes saved successfully.',
        'incomplete_profile' => 'Please complete your profile information.',
        'type_changed' => 'Changes made successfully.',
        'type_change_not_available' => 'This feature is not currently available in the system.',
        'edit' => [
            'error_occurred' => 'An error occurred while editing the user profile. Please try again.',
        ],
        "login_message" => ":name Dear, Your login was successful.",
        "no_mobile_auth" => "You do not have permission to access this section.",
        "sms_not_sent" => "An error occurred while sending SMS, please try again.",
        "mobile_auth_code_sent" => "Verification code was sent to: phone_number.",
        "email_auth_code_sent" => "Verification code sent to email: email.",
        "auth_code_sent" => "Verification code was sent to you.",
        "not_verified_info" => "The submitted information is invalid. Please complete the registration process correctly.",
        "register_failed" => "Sorry, there is a problem with your registration process, please contact support.",
        "login_with_password" => "Enter your password to enter."
    ],
    'cart' => [
        'minimum_purchase_error' => 'To continue shopping, the amount of your shopping cart must be at least <span class = "price-data">: minimum_purchase </span> Tomans.',
        'product_not_found' => 'The product you want is not available in the system.',
        'product_max_count_not_allowed' => 'Inventory: product_title is not enough.',
        'product_not_active' => 'Cannot currently purchase: product_title.'
    ],
    'invoice' => [
        'not_saved' => 'Sorry, there is a problem saving your invoice, please contact support.',
        'no_invoice' => 'You have not created an invoice yet.',
        'not_owner' => 'This invoice does not exist.',
        'cart_submitted' => 'To continue buying, check the address and shipping method.',
        'must_pay_cash' => 'Pay the invoice amount in cash, online payment is not possible.',
        'is_payed' => 'Your invoice has already been paid.',
        'cant_pay' => 'Unable to pay online at this time, please try again later.',
        'payment_initiation_failed' => 'Online payment encountered a problem, please try again.',
        'save_fin_man_error' => 'There was a problem saving the invoice, please try again.',
        'product_max_count_not_allowed' => 'The maximum number of product orders: product_title,: product_count is the number.',
        'product_min_count_not_allowed' => 'The minimum number of product orders: product_title,: product_count is the number.',
        'enabled' => 'The invoice has been reactivated, please pay for it.',
        'not_enabled' => 'There was a problem activating your invoice, please try again.',
        'is_empty' => 'Invoice orders are no longer available',
        'successful' => 'Your Invoice has been completed successfully.',
        'discount_card_status' => [
            'Discount code applied.',
            'This code has already been used.',
            'This code is for someone else.',
            'The discount code is wrong.',
            'The discount code has expired.',
            'The discount code is disabled.',
            'Discount code is not available for some products you purchase.'],
        'pdf_export_failed' => 'There was a problem downloading the pre-invoice',
        'maximum_order_ceiling' => 'Your order has been successfully created. Please contact support to continue submiting your order.',
    ],
    'wish_list' => [
        'attached' => 'Product added to favorites.',
        'detached' => 'The product has been removed from favorites.'
    ],
    'need_list' => [
        'attached' => 'The product was added to the notifications.',
        'detached' => 'The product was removed from the notifications.'
    ],
    'rating' => [
        'submitted' => 'Your comment was successfully submitted.',
        'failed' => 'There was a problem submitting your comment.'
    ],
    'one_time_code' => [
        'invalid_token' => 'The verification code is invalid, please try again.',
        'repeated_request' => 'Your request has been registered, please check your inbox. (Remaining time for next request:: remaining_minutes minutes and: remaining_seconds seconds)',
        'out_of_security_level' => 'Your request exceeded the limit. Please try again later.',
        'invalid_one_time_code' => 'The code entered is incorrect, please try again.',
        'valid_one_time_code' => 'The code entered is correct, please proceed.',
        'invalid_auth_type' => 'There is a problem performing the detection operation, contact support.'
    ],
    'clip_board' => [
        'cut' => [
            'done' => 'Successful cut.',
            'error' => 'Cut had a problem!',
            'invalid_file_action' => 'The desired operation is not available in the system.',
            'invalid_file_type' => 'Please check your input types.',
            'invalid_file_ids' => 'Please check your inputs.',

        ],
        'copy' => [
            'done' => 'Successful copy.',
            'error' => 'Copy encountered!',
            'invalid_file_action' => 'The desired operation is not available in the system.',
            'invalid_file_type' => 'Please check your input typess.',
            'invalid_file_ids' => 'Please check your inputs.',

        ],
        'paste' => [
            'done' => 'Successful operation.',
            'error' => 'Operation encountered a problem!',
            'invalid_file_type' => 'Please check your input types.',
            'empty_clip_board' => 'Your clipboard is empty.',
            'empty_file_object_collection' => 'Please check your inputs.',
            'file_same_destination' => 'This file is in the location.',
            'file_bad_destination' => 'The type of folder you are resetting does not match your file.'
        ],
    ],
    'payment' => [
        'invalid_payment_id' => 'Invalid payment, please try again.',
        'invalid_callback_parameters' => 'Payment information is incorrect, please try again.',
        'payed_once' => 'This payment has already been made, if the amount is deducted from your account again, it will be returned to your account within the next 24 hours.',
        'bad_driver_passed' => 'Payment information is incorrect.',
        'failed' => 'Sorry, your payment failed.',
        'reject_failed' => 'There is a problem returning money to your account, please contact support.',
        'rejected' => 'The paid amount was returned to your account due to a problem in registering the order. Try again.',
        'invalid_invoice' => 'Unfortunately your invoice information is not valid, contact support.',
        'invoice_not_payable' => 'It is not possible to pay online for this invoice, please contact support.',
        'not_available' => 'Payment is not valid, In case of deduction from the account, it will be refunded within the next 24 hours.',
        'successful' => 'Your payment has been completed successfully, the next steps will be notified you via SMS.',
        'charged_back' => 'There was a problem processing your order, please try again. In case of deduction from the account, it will be refunded within the next 24 hours.',
        'not_verified' => 'Your payment is invalid, please contact support.'
    ],
    'web_form_message' => [
        'sent' => 'Your request has been successfully submitted.'
    ],
    'newsletter' => [
        'subscribe' => 'Your newsletter subscription was successful.',
        'unsubscribe' => 'Error subscribing to the newsletter, try again.',
        'dataNotEnough' => 'Settings error',
        'emailFormat' => 'The email format entered is incorrect',
    ],
    'survey' => [
        'invalid_record' => 'Invalid survey, please try again.',
    ],
    'shipment_cost' => [
        'invalid_record' => 'Invalid shipment cost, please try again.',
    ],
    'cart_notification' => [
        'invalid_record' => 'Invalid cart notification, please try again.',
    ],
    'product_package' => [
        'not_exists' => 'Invalid product package, please try again.',
        'item_not_found' => 'Invalid item, please try again.',
        'item_invalid_id' => 'Invalid id, please try again.',
        'item_invalid_count' => 'Invalid count, please try again.',
    ],
    'action_log_setting' => [
        'invalid_record' =>  'Invalid action log setting, please try again.',
    ],
    'action_log' => [
        'unable_to_retrieve_data' => 'Unable to retrieve data, please check database configuration and try again.',
    ],
    'payment_driver' => [
        'invalid_record' =>  'Invalid payment driver data, please try again.',
    ],
];
