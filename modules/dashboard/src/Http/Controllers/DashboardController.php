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
        return view('dashboard');
    }
}
