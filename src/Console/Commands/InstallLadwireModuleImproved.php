<?php

namespace Ladbu\LaravelLadwireModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallLadwireModuleImproved extends Command
{
    protected $signature = 'ladwire:install-improved {module? : The module to install (dashboard, user-management, settings)}
                            {--dashboard : Install dashboard module}
                            {--user-management : Install user management module}
                            {--settings : Install settings module}';
    
    protected $description = 'Install a Ladwire module (improved version with separate route files)';

    public function handle()
    {
        $modules = [];

        if ($this->option('dashboard')) {
            $modules[] = 'dashboard';
        }

        if ($this->option('user-management')) {
            $modules[] = 'user-management';
        }

        if ($this->option('settings')) {
            $modules[] = 'settings';
        }

        // If no modules specified, install all
        if (empty($modules)) {
            $this->installAllModules();
        } else {
            foreach ($modules as $module) {
                $this->installModule($module);
            }
        }

        $this->newLine();
        $this->info('✅ Ladwire Module installation complete!');
        $this->info('📁 Check your app/Http/Controllers folder for installed controllers.');
        $this->info('📁 Check your routes/ folder for module route files.');
        $this->info('⚙️  Run "php artisan vendor:publish --tag=views" to publish views.');
        
        return Command::SUCCESS;
    }

    protected function installAllModules()
    {
        $this->info('Installing all Ladwire modules...');
        $this->installModule('dashboard');
        $this->installModule('user-management');
        $this->installModule('settings');
    }

    protected function installModule($module)
    {
        $this->info("Installing {$this->getModuleName($module)} module...");
        
        // Create Livewire component
        $this->createLivewireComponent($module);
        
        // Create controller
        $this->createController($module);
        
        // Create separate route file
        $this->createRouteFile($module);
        
        // Add route file include to main web.php
        $this->addRouteInclude($module);
        
        // Create view
        $this->createView($module);
        
        // Add to sidebar
        $this->addSidebarItem($module);
        
        $this->info("✅ {$this->getModuleName($module)} module installed");
    }

    protected function createLivewireComponent($module)
    {
        $className = $this->getComponentClassName($module);
        $viewName = $this->getComponentViewName($module);
        
        // Create Livewire component class
        $componentPath = app_path("Livewire/{$className}.php");
        
        // Ensure Livewire directory exists
        $livewireDir = app_path("Livewire");
        if (!File::exists($livewireDir)) {
            File::makeDirectory($livewireDir, 0755, true);
        }
        
        $stub = $this->getLivewireComponentStub($className, $viewName);
        
        File::put($componentPath, $stub);
        $this->info("Created Livewire component: {$componentPath}");
        
        // Create Livewire component view
        $componentViewPath = resource_path("views/livewire/{$viewName}.blade.php");
        
        // Ensure livewire views directory exists
        $livewireViewsDir = resource_path("views/livewire");
        if (!File::exists($livewireViewsDir)) {
            File::makeDirectory($livewireViewsDir, 0755, true);
        }
        
        $viewStub = $this->getLivewireViewStub($viewName);
        
        File::put($componentViewPath, $viewStub);
        $this->info("Created Livewire view: {$componentViewPath}");
    }

    protected function createController($module)
    {
        $controllerClass = $this->getControllerClassName($module);
        $controllerPath = app_path("Http/Controllers/{$controllerClass}.php");
        
        $templatePath = base_path("vendor/ladbu/laravel-ladwire-module/src/Templates/Controllers/{$controllerClass}.php");
        
        if (File::exists($templatePath)) {
            $content = File::get($templatePath);
            File::put($controllerPath, $content);
            $this->info("Created: {$controllerPath}");
        } else {
            $this->error("Controller template not found: {$templatePath}");
        }
    }

    protected function createRouteFile($module)
    {
        $routesDir = base_path('routes');
        $routeFilePath = "{$routesDir}/ladwire-{$module}.php";
        
        // Ensure routes directory exists
        if (!File::exists($routesDir)) {
            File::makeDirectory($routesDir, 0755, true);
        }
        
        $controllerClass = $this->getControllerClassName($module);
        $routeContent = $this->getRouteFileContent($module, $controllerClass);
        
        File::put($routeFilePath, $routeContent);
        $this->info("Created route file: {$routeFilePath}");
    }

    protected function addRouteInclude($module)
    {
        $webRoutesPath = base_path('routes/web.php');
        
        if (File::exists($webRoutesPath)) {
            $routeContent = File::get($webRoutesPath);
            
            $includeLine = "\n// Ladwire Module: {$module}\nrequire __DIR__.'/ladwire-{$module}.php'; // END Ladwire Module: {$module}";
            
            // Only add if not already present
            if (!str_contains($routeContent, "ladwire-{$module}.php")) {
                $routeContent .= $includeLine;
                File::put($webRoutesPath, $routeContent);
                $this->info("Added route include: ladwire-{$module}.php");
            }
        }
    }

    protected function createView($module)
    {
        $viewPath = resource_path("views/ladwire/{$module}.blade.php");
        
        // Ensure ladwire views directory exists
        $viewDir = resource_path("views/ladwire");
        if (!File::exists($viewDir)) {
            File::makeDirectory($viewDir, 0755, true);
        }
        
        $templatePath = base_path("vendor/ladbu/laravel-ladwire-module/src/Templates/Views/{$module}.blade.php");
        
        if (File::exists($templatePath)) {
            $content = File::get($templatePath);
            File::put($viewPath, $content);
            $this->info("Created view: {$viewPath}");
        } else {
            $this->error("View template not found: {$templatePath}");
        }
    }

    protected function addSidebarItem($module)
    {
        $sidebarPath = resource_path('views/layouts/app/sidebar.blade.php');
        
        if (!File::exists($sidebarPath)) {
            $this->warn("Sidebar file not found: {$sidebarPath}");
            return;
        }

        $sidebarContent = File::get($sidebarPath);
        
        $moduleInfo = $this->getModuleInfo($module);
        $sidebarItem = $this->getSidebarItem($moduleInfo);
        
        // Find position to insert the sidebar item (after the dashboard item)
        $pattern = '/(<flux:sidebar\.item[^>]*>Dashboard<\/flux:sidebar\.item>)/';
        
        if (preg_match($pattern, $sidebarContent)) {
            $sidebarItemWithMarkers = "<!-- Ladwire Module: {$module} -->\n                    " . $sidebarItem . "\n                    <!-- END Ladwire Module: {$module} -->";
            $newSidebarContent = preg_replace($pattern, '$1' . "\n                    " . $sidebarItemWithMarkers, $sidebarContent);
            
            if ($newSidebarContent !== null) {
                File::put($sidebarPath, $newSidebarContent);
                $this->info("Added sidebar item for {$moduleInfo['name']}");
            } else {
                $this->error("Failed to add sidebar item for {$moduleInfo['name']}");
            }
        } else {
            // If dashboard item not found, add to the Platform group
            $platformGroupPattern = '/(<flux:sidebar\.group[^>]*heading="[^"]*Platform[^"]*"[^>]*>)/';
            if (preg_match($platformGroupPattern, $sidebarContent)) {
                $newSidebarContent = preg_replace($platformGroupPattern, '$1' . "\n                    " . $sidebarItem, $sidebarContent);
                File::put($sidebarPath, $newSidebarContent);
                $this->info("Added sidebar item for {$moduleInfo['name']}");
            }
        }
    }

    protected function getRouteFileContent($module, $controllerClass)
    {
        return "<?php\n\n// Ladwire Module Routes: {$module}\nuse Illuminate\\Support\\Facades\\Route;\nuse App\\Http\\Controllers\\{$controllerClass};\n\nRoute::get('/{$module}', {$controllerClass}::class)->name('{$module}');\n";
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

    protected function getComponentClassName($module)
    {
        return match($module) {
            'dashboard' => 'LadwireDashboard',
            'user-management' => 'LadwireUserManagement',
            'settings' => 'LadwireSettings',
            default => 'Ladwire' . ucfirst($module)
        };
    }

    protected function getComponentViewName($module)
    {
        return match($module) {
            'dashboard' => 'ladwire-dashboard',
            'user-management' => 'ladwire-user-management',
            'settings' => 'ladwire-settings',
            default => 'ladwire-' . $module
        };
    }

    protected function getControllerClassName($module)
    {
        return match($module) {
            'dashboard' => 'DashboardController',
            'user-management' => 'UserManagementController',
            'settings' => 'SettingsController',
            default => ucfirst($module) . 'Controller'
        };
    }

    protected function getModuleInfo($module)
    {
        return match($module) {
            'dashboard' => [
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
            ],
            'user-management' => [
                'name' => 'User Management',
                'route' => 'user-management',
                'icon' => 'users',
            ],
            'settings' => [
                'name' => 'Settings',
                'route' => 'settings',
                'icon' => 'cog',
            ],
            default => [
                'name' => ucfirst($module),
                'route' => $module,
                'icon' => 'folder-git-2',
            ]
        };
    }

    protected function getSidebarItem($moduleInfo)
    {
        return "<flux:sidebar.item icon=\"{$moduleInfo['icon']}\" :href=\"route('{$moduleInfo['route']}')\" :current=\"request()->routeIs('{$moduleInfo['route']}')\" wire:navigate>\n                        {{ __('{$moduleInfo['name']}') }}\n                    </flux:sidebar.item>";
    }

    protected function getLivewireComponentStub($className, $viewName)
    {
        return <<<PHP
<?php

namespace App\Livewire;

use Livewire\Component;

class {$className} extends Component
{
    public function render()
    {
        return view('livewire.{$viewName}');
    }
}
PHP;
    }

    protected function getLivewireViewStub($viewName)
    {
        return <<<BLADE
<flux:heading>{{ ucfirst(str_replace('-', ' ', str_replace('ladwire-', '', $viewName))) }}</flux:heading>

<div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
    <flux:text>This is the {$viewName} component. Customize this view to add your functionality.</flux:text>
</div>
BLADE;
    }
}
