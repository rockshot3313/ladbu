<?php

use Livewire\Component;

new class extends Component {
    public $settings = [];
    public $siteName = '';
    public $siteDescription = '';
    public $emailSettings = [];
    public $features = [];

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        // Mock data - replace with actual database query
        $this->siteName = 'My Application';
        $this->siteDescription = 'A Laravel application with Ladwire modules';
        $this->emailSettings = [
            'driver' => 'smtp',
            'host' => 'mail.example.com',
            'port' => 587,
            'from_address' => 'noreply@example.com',
            'from_name' => 'My Application',
        ];
        $this->features = [
            'user_registration' => true,
            'email_verification' => true,
            'two_factor_auth' => false,
            'maintenance_mode' => false,
        ];
    }

    public function saveSettings(): void
    {
        // Implement settings save logic
        $this->dispatch('settings-saved');
    }

    // Replace render() with with() to pass data to the view
    public function with(): array
    {
        return [
            'siteName' => $this->siteName,
            'siteDescription' => $this->siteDescription,
            'emailSettings' => $this->emailSettings,
            'features' => $this->features,
        ];
    }
}; ?>

<flux:heading>Application Settings</flux:heading>

<!-- General Settings -->
<div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 mb-6">
    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 mb-4">
        <flux:heading>General Settings</flux:heading>
        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Basic application configuration</flux:text>
    </div>
    
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Site Name</label>
            <input 
                type="text" 
                wire:model="siteName" 
                placeholder="Enter site name"
                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>
        
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Site Description</label>
            <textarea 
                wire:model="siteDescription" 
                placeholder="Enter site description"
                rows="3"
                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
        </div>
        
        <button type="button" wire:click="saveSettings" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
            <flux:icon name="layout-grid" class="w-4 h-4 mr-2" />
            Save Settings
        </button>
    </div>
</div>

<!-- Email Settings -->
<div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 mb-6">
    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 mb-4">
        <flux:heading>Email Settings</flux:heading>
        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Configure email delivery</flux:text>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Mail Driver</label>
            <select wire:model="emailSettings.driver" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="smtp">SMTP</option>
                <option value="mail">Mail</option>
                <option value="sendmail">Sendmail</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Host</label>
            <input 
                type="text" 
                wire:model="emailSettings.host" 
                placeholder="mail.example.com"
                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>
        
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Port</label>
            <input 
                type="number" 
                wire:model="emailSettings.port" 
                placeholder="587"
                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>
        
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">From Address</label>
            <input 
                type="email" 
                wire:model="emailSettings.from_address" 
                placeholder="noreply@example.com"
                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>
    </div>
</div>

<!-- Feature Toggles -->
<div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 mb-4">
        <flux:heading>Features</flux:heading>
        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Enable or disable application features</flux:text>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($features as $key => $enabled)
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $enabled ? 'Enabled' : 'Disabled' }}</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="features.{{ $key }}" class="sr-only peer" {{ $enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-blue-600"></div>
                </label>
            </div>
        @endforeach
    </div>
</div>
