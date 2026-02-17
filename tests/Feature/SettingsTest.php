<?php

namespace Ladbu\LaravelLadwireModule\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Ladbu\LaravelLadwireModule\Http\Livewire\Settings;
use Livewire\Livewire;

class SettingsTest extends RefreshDatabase
{
    /** @test */
    public function settings_component_renders_successfully()
    {
        Livewire::test('settings')
            ->assertSee('Settings')
            ->assertSee('General Settings')
            ->assertSee('Site Name')
            ->assertSee('Site Description')
            ->assertSee('Admin Email')
            ->assertSee('Maximum Users')
            ->assertSee('Enable User Registration')
            ->assertSee('Email Notifications');
    }

    /** @test */
    public function settings_displays_form_fields()
    {
        Livewire::test('settings')
            ->assertViewIs('laravel-ladwire-settings::livewire.settings')
            ->assertSee('Site Name')
            ->assertSee('Site Description')
            ->assertSee('Admin Email')
            ->assertSee('Maximum Users');
    }

    /** @test */
    public function settings_can_save_configuration()
    {
        Livewire::test('settings')
            ->set('siteName', 'Test Site')
            ->set('siteDescription', 'Test Description')
            ->set('adminEmail', 'test@example.com')
            ->set('maxUsers', 50)
            ->call('save')
            ->assertDispatched('settings-saved', 'Settings saved successfully!');
    }

    /** @test */
    public function settings_can_reset_to_defaults()
    {
        Livewire::test('settings')
            ->set('siteName', 'Custom Name')
            ->set('siteDescription', 'Custom Description')
            ->call('resetToDefaults')
            ->assertDispatched('settings-reset', 'Settings reset to defaults!');
    }

    /** @test */
    public function settings_validates_required_fields()
    {
        Livewire::test('settings')
            ->set('siteName', '')
            ->call('save')
            ->assertHasErrors(['siteName' => 'The site name field is required.']);
    }

    /** @test */
    public function settings_can_toggle_registration()
    {
        Livewire::test('settings')
            ->set('enableRegistration', false)
            ->call('save')
            ->assertDispatched('settings-saved');
    }

    /** @test */
    public function settings_can_toggle_notifications()
    {
        Livewire::test('settings')
            ->set('enableEmailNotifications', false)
            ->call('save')
            ->assertDispatched('settings-saved');
    }
}
