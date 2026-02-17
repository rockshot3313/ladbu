<?php

namespace Ladbu\LaravelLadwireSettings\Http\Livewire;

use Livewire\Component;

class SettingsPage extends Component
{
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

    public function render()
    {
        return view('laravel-ladwire-settings::pages.settings.settings');
    }
}
