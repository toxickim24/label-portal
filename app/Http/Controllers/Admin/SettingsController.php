<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService
    ) {
        // Authorization is handled in routes via role middleware
    }

    /**
     * Display branding settings
     */
    public function index(): Response
    {
        $settings = $this->settingsService->getBrandingSettings();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'logoUrl' => $this->settingsService->getLogoUrl('logo'),
            'faviconUrl' => $this->settingsService->getLogoUrl('favicon'),
        ]);
    }

    /**
     * Update branding settings
     */
    public function updateBranding(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
        ]);

        if (isset($validated['app_name'])) {
            $this->settingsService->updateAppName($validated['app_name']);
        }

        if ($request->hasFile('logo')) {
            $this->settingsService->uploadLogo($request->file('logo'), 'logo');
        }

        if ($request->hasFile('favicon')) {
            $this->settingsService->uploadLogo($request->file('favicon'), 'favicon');
        }

        return back()->with('success', 'Branding settings updated successfully.');
    }

    /**
     * Reset to default logos
     */
    public function resetLogos(): RedirectResponse
    {
        $this->settingsService->resetToDefaultLogos();

        return back()->with('success', 'Logos reset to defaults successfully.');
    }
}
