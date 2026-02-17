<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the Laravel Livewire Module is enabled.
    | When set to false, the module's routes and components will not be
    | registered with the application.
    |
    */
    'enabled' => env('LARAVEL_LIVEWIRE_MODULE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This value is the prefix for the module's routes. You can change this
    | to avoid conflicts with existing routes in your application.
    |
    */
    'route_prefix' => env('LARAVEL_LIVEWIRE_MODULE_ROUTE_PREFIX', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be applied to the module's routes. You can
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
    | Livewire Components
    |--------------------------------------------------------------------------
    |
    | Here you can configure which Livewire components are registered by
    | the module. You can disable specific components if you don't need
    | them in your application.
    |
    */
    'components' => [
        'dashboard' => true,
        'user-management' => true,
        'settings' => true,
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
    | Configure the views used by the module. You can override these views
    | by publishing them to your application's resources/views directory.
    |
    */
    'views' => [
        'layout' => 'laravel-livewire-module::layouts.app',
        'dashboard' => 'laravel-livewire-module::livewire.dashboard',
        'user-management' => 'laravel-livewire-module::livewire.user-management',
        'settings' => 'laravel-livewire-module::livewire.settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    |
    | Configure the assets (CSS/JS) used by the module. The module uses
    | Tailwind CSS by default, but you can customize this if needed.
    |
    */
    'assets' => [
        'css' => [
            // Add custom CSS files here
        ],
        'js' => [
            // Add custom JavaScript files here
        ],
    ],
];
