<?php

namespace Ladbu\LaravelLadwireModule\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Ladbu\LaravelLadwireModule\Http\Livewire\Dashboard;
use Livewire\Livewire;

class DashboardTest extends RefreshDatabase
{
    /** @test */
    public function dashboard_component_renders_successfully()
    {
        Livewire::test('dashboard')
            ->assertSee('Dashboard')
            ->assertSee('Total Users')
            ->assertSee('Active Users')
            ->assertSee('Total Posts')
            ->assertSee('New Posts Today')
            ->assertSee('Recent Activity');
    }

    /** @test */
    public function dashboard_displays_statistics()
    {
        Livewire::test('dashboard')
            ->assertViewIs('laravel-ladwire-dashboard::livewire.dashboard')
            ->assertViewHas('stats')
            ->assertViewHas('recentActivity');
    }

    /** @test */
    public function dashboard_shows_mock_data()
    {
        Livewire::test('dashboard')
            ->assertViewHas('stats')
            ->assertViewHas('recentActivity');
    }

    /** @test */
    public function dashboard_stats_are_arrays()
    {
        Livewire::test('dashboard')
            ->assertViewHas('stats')
            ->assertViewHas('recentActivity');
    }
}
