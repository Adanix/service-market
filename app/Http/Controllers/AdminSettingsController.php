<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Services\AuditService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function edit(SettingsService $settings): View
    {
        return view('admin.settings.edit', [
            'settings' => $settings->all(),
        ]);
    }

    public function update(UpdateSettingsRequest $request, SettingsService $settings, AuditService $audit): RedirectResponse
    {
        $validated = $request->validated();

        foreach ($validated as $key => $value) {
            $settings->set($key, $value);
        }

        $audit->log('settings.updated', null, [
            'updated_keys' => array_keys($validated),
        ]);

        return back()->with('status', 'Marketplace settings updated successfully.');
    }
}
