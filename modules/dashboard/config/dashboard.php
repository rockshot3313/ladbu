<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the dashboard module is enabled.
    | When set to false, the dashboard routes and components will not be
    | registered with the application.
    |
    */
    'enabled' => env('LARAVEL_LIVEWIRE_DASHBOARD_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This value is the prefix for the dashboard routes. You can change this
    | to avoid conflicts with existing routes in your application.
    |
    */
    'route_prefix' => env('LARAVEL_LIVEWIRE_DASHBOARD_ROUTE_PREFIX', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be applied to the dashboard routes. You can
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
    | Dashboard Statistics
    |--------------------------------------------------------------------------
    |
    | Configure the statistics displayed on the dashboard. You can customize
    | these values or add your own statistics sources.
    |
    */
    'statistics' => [
        'users' => [
            'enabled' => true,
            'label' => 'Total Users',
            'icon' => 'users',
            'color' => 'blue',
        ],
        'active_users' => [
            'enabled' => true,
            'label' => 'Active Users',
            'icon' => 'check-circle',
            'color' => 'green',
        ],
        'posts' => [
            'enabled' => true,
            'label' => 'Total Posts',
            'icon' => 'document-text',
            'color' => 'purple',
        ],
        'new_posts_today' => [
            'enabled' => true,
            'label' => 'New Posts Today',
            'icon' => 'clock',
            'color' => 'orange',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Recent Activity
    |--------------------------------------------------------------------------
    |
    | Configure the recent activity display on the dashboard.
    |
    */
    'recent_activity' => [
        'enabled' => true,
        'limit' => 10,
        'show_user_avatars' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | Configure the views used by the dashboard module.
    |
    */
    'views' => [
        'dashboard' => 'laravel-livewire-dashboard::livewire.dashboard',
    ],
];
