<?php

namespace Ladbu\LaravelLadwireModule;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Ladbu\LaravelLadwireModule\Console\Commands\InstallLadwireModuleClean;
use Ladbu\LaravelLadwireModule\Console\Commands\InstallLadwireModuleImproved;
use Ladbu\LaravelLadwireModule\Console\Commands\MakeLadwireModule;
use Ladbu\LaravelLadwireModule\Console\Commands\RemoveLadwireModule;
use Ladbu\LaravelLadwireModule\Console\Commands\RemoveLadwireModuleImproved;

class LaravelLadwireModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-ladwire-module.php',
            'laravel-ladwire-module'
        );

        // Register module discovery
        $this->app->singleton('laravel-ladwire-module.modules', function () {
            return $this->discoverModules();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-ladwire-module');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravel-ladwire-module.php' => config_path('laravel-ladwire-module.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-ladwire-module'),
            ], 'views');

            $this->publishes([
                __DIR__.'/../stubs' => resource_path('views/vendor/ladwire-stubs'),
            ], 'ladwire-stubs');
        }

        // Auto-register available modules
        $this->registerAvailableModules();

        // Register installation command for fresh projects
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallLadwireModuleClean::class,
                // InstallLadwireModuleImproved::class, // TODO: Fix autoloading
                MakeLadwireModule::class,
                RemoveLadwireModule::class,
                // RemoveLadwireModuleImproved::class, // TODO: Fix autoloading
            ]);
        }
    }

    /**
     * Discover available modules.
     */
    protected function discoverModules(): array
    {
        $modules = [];
        
        // Check for installed modules
        $modulePackages = [
            'dashboard' => 'Ladbu\\LaravelLadwireDashboard\\DashboardServiceProvider',
            'user-management' => 'Ladbu\\LaravelLadwireUserManagement\\UserManagementServiceProvider',
            'settings' => 'Ladbu\\LaravelLadwireSettings\\SettingsServiceProvider',
        ];

        foreach ($modulePackages as $module => $provider) {
            if (class_exists($provider)) {
                $modules[$module] = [
                    'name' => $module,
                    'provider' => $provider,
                    'enabled' => true,
                ];
            }
        }

        return $modules;
    }

    /**
     * Register available modules.
     */
    protected function registerAvailableModules(): void
    {
        $modules = $this->app->make('laravel-ladwire-module.modules');
        
        foreach ($modules as $module) {
            if ($module['enabled']) {
                $this->app->register($module['provider']);
            }
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'laravel-ladwire-module.modules',
        ];
    }
}
