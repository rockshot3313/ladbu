<?php

namespace Ladbu\LaravelLadwireDashboard\Http\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];
    public $recentActivity = [];

    public function mount()
    {
        $this->stats = [
            'total_users' => 150,
            'active_users' => 45,
            'total_posts' => 320,
            'new_posts_today' => 12,
        ];

        $this->recentActivity = [
            ['user' => 'John Doe', 'action' => 'Created new post', 'time' => '2 minutes ago'],
            ['user' => 'Jane Smith', 'action' => 'Updated profile', 'time' => '5 minutes ago'],
            ['user' => 'Bob Johnson', 'action' => 'Deleted comment', 'time' => '10 minutes ago'],
        ];
    }

    public function render()
    {
        return view('laravel-ladwire-dashboard::livewire.dashboard');
    }
}
