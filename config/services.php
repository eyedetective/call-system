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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'twilio' => [
        'accountSid' => env('TWILIO_ACCOUNT_SID'),
        'accountAuthtoken' => env('TWILIO_ACCOUNT_AUTHTOKEN'),
        'apiKey' => env('TWILIO_API_KEY'),
        'apiSecret' => env('TWILIO_API_SECRET'),
        'applicationSid' => env('TWILIO_APPLICATION_SID'),
        'workspaceSid' => env('TWILIO_WORKSPACE_SID'),
        'workflowSid' => env('TWILIO_WORKFLOW_SID'),
        'number' => env('TWILIO_NUMBER'),
        'activities' => [
            'wrapup'=>''
        ]
    ],

];
