<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SettingsService
{
    /**
     * Get all branding settings
     */
    public function getBrandingSettings(): array
    {
        return Setting::getByGroup('branding');
    }

    /**
     * Update branding settings
     */
    public function updateBrandingSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            if ($value instanceof UploadedFile) {
                // Handle file upload
                $path = $value->store('logos', 'public');
                Setting::set($key, $path, 'string', 'branding');
            } else {
                Setting::set($key, $value, 'string', 'branding');
            }
        }
    }

    /**
     * Upload logo
     */
    public function uploadLogo(UploadedFile $file, string $type): string
    {
        // Delete old logo if exists
        $oldPath = Setting::get("logo_{$type}");
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Store new logo
        $path = $file->store('logos', 'public');
        Setting::set("logo_{$type}", $path, 'string', 'branding');

        return $path;
    }

    /**
     * Get logo URL
     */
    public function getLogoUrl(string $type, bool $darkMode = false): ?string
    {
        // Check for custom uploaded logo
        $customLogo = Setting::get("logo_{$type}");
        if ($customLogo) {
            return Storage::disk('public')->url($customLogo);
        }

        // Return default logo
        if ($type === 'logo') {
            $filename = $darkMode ? "mcmullen-logo-white.png" : "mcmullen-logo-blue.png";
        } else {
            $filename = "mcmullen-favicon.png";
        }
        return asset("images/logos/{$filename}");
    }

    /**
     * Reset to default logos
     */
    public function resetToDefaultLogos(): void
    {
        $types = ['logo', 'favicon'];

        foreach ($types as $type) {
            $customPath = Setting::get("logo_{$type}");
            if ($customPath && Storage::disk('public')->exists($customPath)) {
                Storage::disk('public')->delete($customPath);
            }
            Setting::set("logo_{$type}", null, 'string', 'branding');
        }
    }

    /**
     * Get all settings by group
     */
    public function getSettingsByGroup(string $group): array
    {
        return Setting::getByGroup($group);
    }

    /**
     * Get a single setting
     */
    public function getSetting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * Set a single setting
     */
    public function setSetting(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        Setting::set($key, $value, $type, $group);
    }

    /**
     * Get app name
     */
    public function getAppName(): string
    {
        return Setting::get('app_name', config('app.name'));
    }

    /**
     * Update app name
     */
    public function updateAppName(string $name): void
    {
        Setting::set('app_name', $name, 'string', 'branding');
    }
}
