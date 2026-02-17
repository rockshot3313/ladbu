<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Flux UI Assets -->
    <flux:styles />
    
    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="bg-gray-50">
    <flux:prose>
        <div class="min-h-screen">
            <!-- Navigation -->
            <flux:navbar>
                <flux:navbar.brand>
                    {{ config('app.name') }}
                </flux:navbar.brand>
                
                <flux:navbar.menu>
                    <flux:navbar.item>
                        <flux:link href="{{ route('module.dashboard') }}" wire:navigate>
                            Dashboard
                        </flux:link>
                    </flux:navbar.item>
                    <flux:navbar.item>
                        <flux:link href="{{ route('module.users') }}" wire:navigate>
                            Users
                        </flux:link>
                    </flux:navbar.item>
                    <flux:navbar.item>
                        <flux:link href="{{ route('module.settings') }}" wire:navigate>
                            Settings
                        </flux:link>
                    </flux:navbar.item>
                </flux:navbar.menu>
                
                <flux:navbar.actions>
                    <flux:dropdown>
                        <flux:button variant="ghost" size="sm">
                            <flux:icon.user />
                            User
                        </flux:button>
                        
                        <flux:dropdown.menu>
                            <flux:dropdown.item>Profile</flux:dropdown.item>
                            <flux:dropdown.item>Settings</flux:dropdown.item>
                            <flux:dropdown.separator />
                            <flux:dropdown.item variant="danger">Logout</flux:dropdown.item>
                        </flux:dropdown.menu>
                    </flux:dropdown>
                </flux:navbar.actions>
            </flux:navbar>

            <!-- Page Content -->
            <main class="container mx-auto px-4 py-8">
                @yield('content')
            </main>
        </div>
    </flux:prose>

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Flux UI Scripts -->
    <flux:scripts />
</body>
</html>
