<?php

namespace Ladbu\LaravelLadwireDashboard;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dashboard.php',
            'laravel-ladwire-dashboard'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-ladwire-dashboard');
        
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dashboard.php' => config_path('laravel-ladwire-dashboard.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-ladwire-dashboard'),
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
        Livewire::component('laravel-ladwire-dashboard::dashboard', \Ladbu\LaravelLadwireDashboard\Http\Livewire\Dashboard::class);
        
        // Register page component
        Livewire::component('laravel-ladwire-dashboard::dashboard-page', \Ladbu\LaravelLadwireDashboard\Http\Livewire\DashboardPage::class);
        
        // Register inline component following starter kit pattern
        Livewire::component('pages::dashboard', \Ladbu\LaravelLadwireDashboard\Http\Livewire\DashboardPage::class);
    }
}
