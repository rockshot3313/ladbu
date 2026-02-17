<?php

namespace Ladbu\LaravelLadwireUserManagement;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class UserManagementServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/user-management.php',
            'laravel-ladwire-user-management'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-ladwire-user-management');
        
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/user-management.php' => config_path('laravel-ladwire-user-management.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-ladwire-user-management'),
            ], 'views');
        }

        $this->registerLadwireComponents();
    }

    /**
     * Register Ladwire components.
     */
    protected function registerLadwireComponents(): void
    {
        // Register traditional Livewire component
        Livewire::component('laravel-ladwire-user-management::user-management', \Ladbu\LaravelLadwireUserManagement\Http\Livewire\UserManagement::class);
        
        // Register page component
        Livewire::component('laravel-ladwire-user-management::user-management-page', \Ladbu\LaravelLadwireUserManagement\Http\Livewire\UserManagementPage::class);
        
        // Register inline component following starter kit pattern
        Livewire::component('pages::user-management', \Ladbu\LaravelLadwireUserManagement\Http\Livewire\UserManagementPage::class);
    }
}
