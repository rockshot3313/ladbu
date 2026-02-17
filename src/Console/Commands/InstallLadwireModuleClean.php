<?php

namespace Ladbu\LaravelLadwireModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class InstallLadwireModuleClean extends Command
{
    protected $signature = 'ladwire:install-clean {module? : The module to install (dashboard, user-management, settings)}
                            {--dashboard : Install dashboard module}
                            {--user-management : Install user management module}
                            {--settings : Install settings module}';
    
    protected $description = 'Install a Ladwire module (clean version with external templates)';

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
        $this->info('📁 Check your resources/views/pages folder for installed components.');
        $this->info('📁 Check your routes/web.php for added routes.');
        
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
        
        // Create single-file Livewire component
        $this->createLivewireComponent($module);
        
        // Create route
        $this->addRoute($module);
        
        // Add to sidebar
        $this->addSidebarItem($module);
        
        $this->info("✅ {$this->getModuleName($module)} module installed");
    }

    protected function createLivewireComponent($module)
    {
        $viewName = $this->getComponentViewName($module);
        
        // Create single-file Livewire component in pages directory
        $componentPath = resource_path("views/pages/{$module}/⚡{$viewName}.blade.php");
        
        // Ensure pages directory exists
        $pagesDir = resource_path("views/pages/{$module}");
        if (!File::exists($pagesDir)) {
            File::makeDirectory($pagesDir, 0755, true);
        }
        
        $templatePath = base_path("vendor/ladbu/laravel-ladwire-module/modules/{$module}/resources/views/pages/⚡{$module}.blade.php");
        
        if (File::exists($templatePath)) {
            $content = File::get($templatePath);
            File::put($componentPath, $content);
            $this->info("Created Livewire component: {$componentPath}");
        } else {
            $this->error("Component template not found: {$templatePath}");
        }
    }

    protected function addRoute($module)
    {
        $webRoutesPath = base_path('routes/web.php');
        
        if (File::exists($webRoutesPath)) {
            $routeContent = File::get($webRoutesPath);
            
            // Add route with unique identifiers pointing to single-file component
            $routeLine = "\n// Ladwire Module: {$module}\nRoute::get('/{$module}', function () {\n    return view('pages.{$module}.⚡{$this->getComponentViewName($module)}');\n})->name('{$module}'); // END Ladwire Module: {$module}";
            $routeContent .= $routeLine;
            
            File::put($webRoutesPath, $routeContent);
            $this->info("Added route: /{$module}");
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
            File::put($sidebarPath, $newSidebarContent);
            $this->info("Added sidebar item for {$moduleInfo['name']}");
        } else {
            // If dashboard item not found, add to the Platform group
            $platformGroupPattern = '/(<flux:sidebar\.group[^>]*heading="[^"]*Platform[^"]*"[^>]*>)/';
            if (preg_match($platformGroupPattern, $sidebarContent)) {
                $sidebarItemWithMarkers = "<!-- Ladwire Module: {$module} -->\n                    " . $sidebarItem . "\n                    <!-- END Ladwire Module: {$module} -->";
                $newSidebarContent = preg_replace($platformGroupPattern, '$1' . "\n                    " . $sidebarItemWithMarkers, $sidebarContent);
                File::put($sidebarPath, $newSidebarContent);
                $this->info("Added sidebar item for {$moduleInfo['name']}");
            }
        }
    }

    protected function getModuleName($module)
    {
        return match($module) {
            'dashboard' => 'Dashboard',
            'user-management' => 'User Management',
            'settings' => 'Settings',
            default => ucfirst($module),
        };
    }

    protected function getComponentViewName($module)
    {
        return match($module) {
            'dashboard' => 'ladwire-dashboard',
            'user-management' => 'ladwire-user-management',
            'settings' => 'ladwire-settings',
            default => 'ladwire-' . $module,
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
}
