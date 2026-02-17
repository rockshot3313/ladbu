<?php

namespace Ladbu\LaravelLadwireUserManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserManagementController
{
    /**
     * Display the user management view.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        return view('laravel-ladwire-user-management::user-management');
    }
}
