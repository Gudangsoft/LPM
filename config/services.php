<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // API SIAKAD (wslive/apidata). Kredensial hanya di .env server, jangan di-commit.
    'siakad' => [
        'url' => env('SIAKAD_API_URL', 'https://siakad.unukase.ac.id/wslive/apidata'),
        'username' => env('SIAKAD_API_USERNAME'),
        'password' => env('SIAKAD_API_PASSWORD'),
        'timeout' => (int) env('SIAKAD_API_TIMEOUT', 60),
        'page_size' => (int) env('SIAKAD_API_PAGE_SIZE', 1000),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
