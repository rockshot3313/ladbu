<?php

namespace Ladbu\LaravelLadwireSettings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController
{
    /**
     * Display the settings view.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        return view('settings');
    }
}
