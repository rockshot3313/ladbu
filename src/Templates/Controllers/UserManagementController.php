<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserManagementController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        // Mock data - replace with actual database query
        $users = collect([
            (object)['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'admin', 'created_at' => '2024-01-15'],
            (object)['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'user', 'created_at' => '2024-01-16'],
            (object)['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'role' => 'user', 'created_at' => '2024-01-17'],
        ]);

        return view('ladwire.user-management', compact('users'));
    }
}
