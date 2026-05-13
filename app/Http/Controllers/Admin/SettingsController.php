<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('system_settings.view')), 403);

        $companyId = $user->company_id;

        $settings = SystemSetting::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->get();

        return Inertia::render('Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('system_settings.update')), 403);

        $data = $request->validate([
            'settings' => 'array',
            'settings.*.id' => 'required|integer',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($data['settings'] ?? [] as $row) {
            SystemSetting::where('id', $row['id'])->update(['value' => $row['value']]);
        }

        return back()->with('success', __('coffee.updated_successfully'));
    }
}
