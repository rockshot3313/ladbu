<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the settings module is enabled.
    | When set to false, the settings routes and components will not be
    | registered with the application.
    |
    */
    'enabled' => env('LARAVEL_LIVEWIRE_SETTINGS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This value is the prefix for the settings routes. You can change this
    | to avoid conflicts with existing routes in your application.
    |
    */
    'route_prefix' => env('LARAVEL_LIVEWIRE_SETTINGS_ROUTE_PREFIX', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be applied to the settings routes. You can
    | customize this to fit your application's authentication and
    | authorization requirements.
    |
    */
    'route_middleware' => [
        'web',
        'auth', // Uncomment if you want to require authentication
        // 'admin', // Add your custom admin middleware if needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings Storage
    |--------------------------------------------------------------------------
    |
    | Configure how settings are stored. Options are:
    | - 'config' - Store in configuration files
    | - 'database' - Store in database table
    | - 'cache' - Store in cache
    |
    */
    'storage' => env('LARAVEL_LIVEWIRE_SETTINGS_STORAGE', 'config'),

    /*
    |--------------------------------------------------------------------------
    | Settings Groups
    |--------------------------------------------------------------------------
    |
    | Configure the settings groups available in the settings panel.
    |
    */
    'groups' => [
        'general' => [
            'title' => 'General Settings',
            'description' => 'Basic application settings',
            'icon' => 'cog-6-tooth',
        ],
        'security' => [
            'title' => 'Security Settings',
            'description' => 'Security and authentication settings',
            'icon' => 'shield-check',
        ],
        'notifications' => [
            'title' => 'Notification Settings',
            'description' => 'Email and notification preferences',
            'icon' => 'bell',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | These are the default settings for the module. These values can be
    | overridden in your application's .env file or through the settings
    | interface in the admin panel.
    |
    */
    'defaults' => [
        'site_name' => env('APP_NAME', 'Laravel App'),
        'site_description' => 'A Laravel application with Livewire module',
        'admin_email' => env('MAIL_FROM_ADDRESS', 'admin@example.com'),
        'enable_registration' => true,
        'enable_email_notifications' => true,
        'max_users' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | Configure the views used by the settings module.
    |
    */
    'views' => [
        'settings' => 'laravel-livewire-settings::livewire.settings',
    ],
];
