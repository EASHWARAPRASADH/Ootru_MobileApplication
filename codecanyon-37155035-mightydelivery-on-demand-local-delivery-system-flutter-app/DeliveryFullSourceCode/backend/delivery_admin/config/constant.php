<?php
return [
    'IMAGE_EXTENTIONS' => ['png','jpg','jpeg','gif'],
    'PER_PAGE_LIMIT' => 10,
    'MAIL_SETTING' => [
        'MAIL_MAILER' => env('MAIL_MAILER'),
        'MAIL_HOST' => env('MAIL_HOST'),
        'MAIL_PORT' => env('MAIL_PORT'),
        'MAIL_USERNAME' => env('MAIL_USERNAME'),
        'MAIL_PASSWORD' => env('MAIL_PASSWORD'),
        'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
        'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
        'EMAIL_OTP_VERIFICATION' => env('EMAIL_OTP_VERIFICATION'),
    ],
    'MAIL_PLACEHOLDER' => [
        'MAIL_MAILER' => 'smtp',
        'MAIL_HOST' => 'smtp.gmail.com',
        'MAIL_PORT' => '587',
        'MAIL_ENCRYPTION' => 'tls',
        'MAIL_USERNAME' => 'youremail@gmail.com',
        'MAIL_PASSWORD' => 'Password',
        'MAIL_FROM_ADDRESS' => 'youremail@gmail.com',
    ],

    'PAYMENT_GATEWAY_SETTING' => [
        'stripe' => [ 'url', 'secret_key', 'publishable_key' ],
        'razorpay' => [ 'key_id', 'secret_id' ],
        'paystack' => [ 'public_key' ],
        'flutterwave' => [ 'public_key', 'secret_key', 'encryption_key' ],
         'paypal' => [ 'tokenization_key', 'public_key', 'secret_key' ],
        'paytabs' => [ 'client_key', 'profile_id', 'server_key'],
        // 'mercadopago' => [ 'public_key', 'access_token' ],
        'paytm' => [ 'merchant_id', 'merchant_key' ],
        'Paytr' => [ 'merchant_id', 'merchant_key','merchant_salt' ],
    ],

    'notification' => [
        'IS_ONESIGNAL' => '',
        // 'IS_FIREBASE' => '',
    ],

    'pages' =>[
        'title' => '',
        'description' => '',
    ],

    'app_content' => [
        'app_name' => '',
        'app_title' => '',
        'app_subtitle' => '',
        'delivery_man_image' => '',
        'app_logo_image' => '',
        'play_store_link' => '',
        'app_store_link' => '',
        'trust_pilot_link' => '',
        'playstore_image' => '',
        'appstore_image' => ''

    ],

    'why_choose' => [
        'title' => '',
        'description' => '',
    ],

    'app_overview' => [
        'title' => '',
        'subtitle' => ''
    ],

    'client_review' => [
        'client_review_title' => '',
    ],

    'download_app' => [
        'download_title' => '',
        'download_subtitle' => '',
        'download_description' => '',
        'download_footer_content' => '',
        'download_app_logo' => '',
    ],

    'delivery_partner' => [
        'title' => '',
        'subtitle' => '',
        'description' => '',
    ],

    'contact_us' => [
        'contact_title' => '',
        'contact_subtitle' => '',
        'contact_us_app_ss' => '',
    ],

    'about_us' => [
        'download_title' => '',
        'download_subtitle' => '',
        'long_des' => '',
        'about_us_app_ss' => '',
    ],

    'track_order' => [
        'track_order_title' => '',
        'track_order_subtitle' => '',
        'track_page_title' => '',
        'track_page_description' => '',
    ],

    'order_invoice' => [
        'company_name' => '',
        'company_contact_number' => '',
        'company_address' => '',
        'company_logo' => ''
    ],
    'SMS_SETTING' => [
        'twilio' => [ 'sid', 'token', 'service_sid', 'from'],
        // '2factor' => ['api_key'],
        // 'msg91' => ['template_id', 'auth_key'],
        // 'nexmo' => [ 'api_key', 'api_secret', 'token', 'from', 'otp_template'],
        // 'alphanet_sms' => [ 'api_key', 'otp_template'],
    ],

    'SMS_TEMPLATE_SETTING' => [
        'create' => '',
        'courier_assigned' => '',
        'courier_picked_up' => '',
        'completed' => '',
        'sms_template_description' => '',
    ],

    'client_testimonial' => [
        'title'                  => '',
        'subtitle'               => '',
        'playstore_totalreview'  => '',
        'appstore_totalreview'   => '',
        'trustpilot_totalreview' => '',
        'playstore_review'       => '',
        'appstore_review'        => '',
        'trustpilot_review'      => '',
    ],

    'document_verification' => [
        'title' => '',
        'subtitle' => '',
        'description' => '',
    ],

    'delivery_man_section' => [
        'title' => '',
        'subtitle' => ''
    ],

    'deliver_your_way' => [
        'title' => '',
        'subtitle' => '',
        'description' => '',
    ],

    'courier_recruitment_section' => [
        'courier_title' => '',
        'courier_description' => '',
        'courier_image' => '',
    ],

    'delivery_job' => [
        'delivery_job_title' => '',
        'delivery_job_subtitle' => '',
        'delivery_job_description' => '',
        'delivery_job_image' => '',
    ],

];
