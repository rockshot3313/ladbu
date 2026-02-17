<?php

namespace Ladbu\LaravelLadwireDashboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController
{
    /**
     * Display the dashboard view.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $stats = [
            'total_users' => 150,
            'active_users' => 45,
            'total_posts' => 320,
            'new_posts_today' => 12,
        ];

        $recentActivity = [
            ['user' => 'John Doe', 'action' => 'Created new post', 'time' => '2 minutes ago'],
            ['user' => 'Jane Smith', 'action' => 'Updated profile', 'time' => '5 minutes ago'],
            ['user' => 'Bob Johnson', 'action' => 'Deleted comment', 'time' => '10 minutes ago'],
        ];

        return view('laravel-ladwire-dashboard::dashboard', compact('stats', 'recentActivity'));
    }
}
