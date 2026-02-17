<?php

namespace Ladbu\LaravelLadwireSettings;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/settings.php',
            'laravel-ladwire-settings'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-ladwire-settings');
        
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/settings.php' => config_path('laravel-ladwire-settings.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-ladwire-settings'),
            ], 'views');
        }

        $this->registerLadwireComponents();
    }

    /**
     * Register Ladwire components.
     */
    protected function registerLadwireComponents(): void
    {
        Livewire::component('laravel-ladwire-settings::settings', \Ladbu\LaravelLadwireSettings\Http\Livewire\Settings::class);
    }
}
