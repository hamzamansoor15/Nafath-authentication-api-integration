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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'nafath' => [
        // stage
        'stage_app_id' => env('NAFATH_STAGE_APP_ID'),
        'stage_app_key' => env('NAFATH_STAGE_APP_KEY'),
        'stage_base_url'=>env('NAFATH_STAGE_BASE_URL'),

        // prod
        'prod_app_id' => env('NAFATH_PROD_APP_ID'),
        'prod_app_key' => env('NAFATH_PROD_APP_KEY'),
        'prod_base_url' =>env('NAFATH_PROD_BASE_URL'),

    ],

];
