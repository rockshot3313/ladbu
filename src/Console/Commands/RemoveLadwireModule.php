<?php

namespace Ladbu\LaravelLadwireModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RemoveLadwireModule extends Command
{
    protected $signature = 'ladwire:remove {module : The module to remove (dashboard, user-management, settings)}';
    protected $description = 'Remove a Ladwire module';

    public function handle()
    {
        $module = $this->argument('module');
        
        if (!$this->isValidModule($module)) {
            $this->error("Invalid module: {$module}");
            $this->info("Available modules: dashboard, user-management, settings");
            return 1;
        }

        $this->info("Removing Ladwire Module...");
        $this->info("Removing {$this->getModuleName($module)} module...");

        $this->removeController($module);
        $this->removeLivewireComponent($module);
        $this->removeViews($module);
        $this->removeRoute($module);

        $this->info("✅ {$this->getModuleName($module)} module removed");
        $this->info("✅ Ladwire Module removal complete!");

        return 0;
    }

    protected function isValidModule($module)
    {
        return in_array($module, ['dashboard', 'user-management', 'settings']);
    }

    protected function getModuleName($module)
    {
        return match($module) {
            'dashboard' => 'Dashboard',
            'user-management' => 'User Management',
            'settings' => 'Settings',
            default => ucfirst($module)
        };
    }

    protected function getControllerClass($module)
    {
        return match($module) {
            'dashboard' => 'DashboardController',
            'user-management' => 'UserManagementController',
            'settings' => 'SettingsController',
            default => ucfirst($module) . 'Controller'
        };
    }

    protected function getLivewireClass($module)
    {
        return match($module) {
            'dashboard' => 'LadwireDashboard',
            'user-management' => 'LadwireUserManagement',
            'settings' => 'LadwireSettings',
            default => 'Ladwire' . ucfirst($module)
        };
    }

    protected function getRoutePath($module)
    {
        return match($module) {
            'dashboard' => '/dashboard',
            'user-management' => '/user-management',
            'settings' => '/settings',
            default => '/' . $module
        };
    }

    protected function removeController($module)
    {
        $controllerClass = $this->getControllerClass($module);
        $controllerPath = app_path("Http/Controllers/{$controllerClass}.php");

        if (File::exists($controllerPath)) {
            File::delete($controllerPath);
            $this->info("Removed controller: {$controllerPath}");
        }
    }

    protected function removeLivewireComponent($module)
    {
        $livewireClass = $this->getLivewireClass($module);
        $livewirePath = app_path("Livewire/{$livewireClass}.php");

        if (File::exists($livewirePath)) {
            File::delete($livewirePath);
            $this->info("Removed Livewire component: {$livewirePath}");
        }

        // Remove Livewire view
        $livewireViewPath = resource_path("views/livewire/" . Str::kebab($livewireClass) . ".blade.php");
        if (File::exists($livewireViewPath)) {
            File::delete($livewireViewPath);
            $this->info("Removed Livewire view: {$livewireViewPath}");
        }
    }

    protected function removeViews($module)
    {
        $viewPath = resource_path("views/ladwire/{$module}.blade.php");

        if (File::exists($viewPath)) {
            File::delete($viewPath);
            $this->info("Removed view: {$viewPath}");
        }
    }

    protected function removeRoute($module)
    {
        $webRoutesPath = base_path('routes/web.php');
        
        if (File::exists($webRoutesPath)) {
            $routeContent = File::get($webRoutesPath);
            $routePath = $this->getRoutePath($module);
            $controllerClass = $this->getControllerClass($module);
            
            // Remove the route line
            $pattern = "/Route::get\(['\"]{$routePath}['\"],\s*{$controllerClass}::class\)\s*->name\(['\"][^'\"]*['\"]\);?/";
            $routeContent = preg_replace($pattern, '', $routeContent);
            
            // Remove empty lines
            $routeContent = preg_replace("/\n\s*\n\s*\n/", "\n\n", $routeContent);
            
            File::put($webRoutesPath, $routeContent);
            $this->info("Removed route: {$routePath}");
        }
    }
}
