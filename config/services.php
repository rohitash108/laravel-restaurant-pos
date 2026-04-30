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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Razorpay (Platform Master Account / Route)
    |--------------------------------------------------------------------------
    |
    | These keys belong to the SaaS platform's master Razorpay account, which
    | holds the Route partnership. Each restaurant is onboarded as a Linked
    | Account under this master and money is settled to their bank via the
    | transfers[] field on orders.
    |
    | Per-outlet keys (where each restaurant has their own independent Razorpay
    | account) live in the per-restaurant Setting model under the
    | "payment_gateways" group and are unrelated to these master keys.
    */
    'razorpay' => [
        'mode'                 => env('RAZORPAY_MODE', 'route'), // 'route' | 'per_outlet' | 'auto'
        'master_key_id'        => env('RAZORPAY_MASTER_KEY_ID'),
        'master_key_secret'    => env('RAZORPAY_MASTER_KEY_SECRET'),
        'webhook_secret'       => env('RAZORPAY_WEBHOOK_SECRET'),
        'platform_fee_percent' => (float) env('RAZORPAY_PLATFORM_FEE_PERCENT', 0),
        // Razorpay account id (acc_XXX) of the *platform itself* — used in webhook
        // payload validation so we know the event is for our master, not someone else's.
        'platform_account_id'  => env('RAZORPAY_PLATFORM_ACCOUNT_ID'),
    ],

];
