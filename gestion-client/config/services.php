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

    'keycloak' => [
        'client_id'                => env('KEYCLOAK_CLIENT_ID'),
        'client_secret'            => env('KEYCLOAK_CLIENT_SECRET'),
        'redirect'                 => env('KEYCLOAK_REDIRECT_URI', env('APP_URL', 'http://localhost:8000').'/auth/callback'),
        'base_url'                 => env('KEYCLOAK_BASE_URL', 'http://localhost:8080'),
        'realms'                   => env('KEYCLOAK_REALM', 'CompanyRealm'),
        'scopes'                   => explode(',', env('KEYCLOAK_SCOPES', 'openid,profile,email')),
        // Client de service pour l'API Admin Keycloak
        'admin_client_id'          => env('KEYCLOAK_ADMIN_CLIENT_ID', 'laravel-admin-client'),
        'admin_client_secret'      => env('KEYCLOAK_ADMIN_CLIENT_SECRET', ''),
        'admin_notification_email' => env('ADMIN_NOTIFICATION_EMAIL', 'admin@gestion-client.com'),
        'admin_user_url'           => env('KEYCLOAK_ADMIN_USER_URL', 'https://keycloak.example.com/admin/realms/CompanyRealm/users/'),
    ],

];
