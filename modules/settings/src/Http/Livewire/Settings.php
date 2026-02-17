<?php

namespace Ladbu\LaravelLadwireSettings\Http\Livewire;

use Livewire\Component;

class Settings extends Component
{
    public $siteName = '';
    public $siteDescription = '';
    public $adminEmail = '';
    public $enableRegistration = true;
    public $enableEmailNotifications = true;
    public $maxUsers = 100;

    public function mount()
    {
        $this->siteName = config('app.name', 'Laravel App');
        $this->siteDescription = 'Default site description';
        $this->adminEmail = config('mail.from.address', 'admin@example.com');
    }

    public function render()
    {
        return view('laravel-ladwire-settings::livewire.settings');
    }

    public function save()
    {
        $this->validate([
            'siteName' => 'required|string|max:255',
            'siteDescription' => 'required|string|max:500',
            'adminEmail' => 'required|email',
            'maxUsers' => 'required|integer|min:1',
        ]);

        // In a real implementation, you would save these settings to config or database
        $this->dispatch('settings-saved', 'Settings saved successfully!');
    }

    public function resetToDefaults()
    {
        $this->siteName = config('app.name', 'Laravel App');
        $this->siteDescription = 'Default site description';
        $this->adminEmail = config('mail.from.address', 'admin@example.com');
        $this->enableRegistration = true;
        $this->enableEmailNotifications = true;
        $this->maxUsers = 100;

        $this->dispatch('settings-reset', 'Settings reset to defaults!');
    }
}
