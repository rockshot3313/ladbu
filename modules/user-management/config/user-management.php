<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Management Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the user management module is enabled.
    | When set to false, the user management routes and components will not be
    | registered with the application.
    |
    */
    'enabled' => env('LARAVEL_LIVEWIRE_USER_MANAGEMENT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This value is the prefix for the user management routes. You can change this
    | to avoid conflicts with existing routes in your application.
    |
    */
    'route_prefix' => env('LARAVEL_LIVEWIRE_USER_MANAGEMENT_ROUTE_PREFIX', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be applied to the user management routes. You can
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
    | User Model
    |--------------------------------------------------------------------------
    |
    | Configure the user model class used by the user management module.
    |
    */
    'user_model' => env('LARAVEL_LIVEWIRE_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | User Permissions
    |--------------------------------------------------------------------------
    |
    | Configure the permissions for user management operations.
    |
    */
    'permissions' => [
        'view_users' => 'users.view',
        'create_users' => 'users.create',
        'edit_users' => 'users.edit',
        'delete_users' => 'users.delete',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Configure pagination settings for the user management module.
    |
    */
    'pagination' => [
        'per_page' => 10,
        'per_page_options' => [10, 25, 50, 100],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Roles
    |--------------------------------------------------------------------------
    |
    | Configure the available user roles in the system.
    |
    */
    'roles' => [
        'user' => 'User',
        'admin' => 'Administrator',
        // Add more roles as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | Configure the views used by the user management module.
    |
    */
    'views' => [
        'user_management' => 'laravel-livewire-user-management::livewire.user-management',
    ],
];
