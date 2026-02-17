<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function __invoke()
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

        return view('ladwire.dashboard', compact('stats', 'recentActivity'));
    }
}
