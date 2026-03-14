<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = $this->getSettings();
        
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string|max:500',
            'school_phone' => 'required|string|max:50',
            'school_email' => 'required|email|max:255',
            'school_website' => 'nullable|string|max:255',
            'principal_name' => 'required|string|max:255',
            'principal_signature' => 'nullable|string|max:255',
            'authorized_signature_text' => 'nullable|string|max:255',
            'show_map' => 'nullable',
            'school_map_embed' => 'nullable|string|max:5000',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'school_favicon' => 'nullable|image|mimes:jpeg,png,jpg,ico|max:1024',
            'signature_image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'office_hours_school_mf_status' => 'nullable|string|in:open,closed',
            'office_hours_school_mf_open' => 'nullable|string|max:20',
            'office_hours_school_mf_close' => 'nullable|string|max:20',
            'office_hours_school_sat_status' => 'nullable|string|in:open,closed',
            'office_hours_school_sat_open' => 'nullable|string|max:20',
            'office_hours_school_sat_close' => 'nullable|string|max:20',
            'office_hours_school_sun_status' => 'nullable|string|in:open,closed',
            'office_hours_school_sun_open' => 'nullable|string|max:20',
            'office_hours_school_sun_close' => 'nullable|string|max:20',
            'office_hours_admission_mf_status' => 'nullable|string|in:open,closed',
            'office_hours_admission_mf_open' => 'nullable|string|max:20',
            'office_hours_admission_mf_close' => 'nullable|string|max:20',
            'office_hours_admission_sat_status' => 'nullable|string|in:open,closed',
            'office_hours_admission_sat_open' => 'nullable|string|max:20',
            'office_hours_admission_sat_close' => 'nullable|string|max:20',
            'office_hours_admission_sun_status' => 'nullable|string|in:open,closed',
            'office_hours_admission_sun_open' => 'nullable|string|max:20',
            'office_hours_admission_sun_close' => 'nullable|string|max:20',
            'social_facebook' => 'nullable|url|max:500',
            'social_twitter' => 'nullable|url|max:500',
            'social_instagram' => 'nullable|url|max:500',
            'social_youtube' => 'nullable|url|max:500',
            'social_linkedin' => 'nullable|url|max:500',
            'social_whatsapp' => 'nullable|url|max:500',
        ]);

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            // Delete old logo if exists
            $oldLogo = $this->getSetting('school_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $logoPath = $request->file('school_logo')->store('settings', 'public');
            $this->setSetting('school_logo', $logoPath);
        }

        // Handle logo removal
        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            $oldLogo = $this->getSetting('school_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $this->setSetting('school_logo', null);
        }

        // Handle favicon upload
        if ($request->hasFile('school_favicon')) {
            // Delete old favicon if exists
            $oldFavicon = $this->getSetting('school_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            $faviconPath = $request->file('school_favicon')->store('settings', 'public');
            $this->setSetting('school_favicon', $faviconPath);
        }

        // Handle favicon removal
        if ($request->has('remove_favicon') && $request->remove_favicon == '1') {
            $oldFavicon = $this->getSetting('school_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $this->setSetting('school_favicon', null);
        }

        // Handle signature image upload
        if ($request->hasFile('signature_image')) {
            // Delete old signature if exists
            $oldSignature = $this->getSetting('signature_image');
            if ($oldSignature && Storage::disk('public')->exists($oldSignature)) {
                Storage::disk('public')->delete($oldSignature);
            }

            $signaturePath = $request->file('signature_image')->store('settings/signatures', 'public');
            $this->setSetting('signature_image', $signaturePath);
        }

        // Handle show_map toggle (unchecked checkbox won't be in validated)
        $this->setSetting('show_map', $request->boolean('show_map') ? '1' : '');

        // Save other settings
        foreach ($validated as $key => $value) {
            if (!in_array($key, ['school_logo', 'school_favicon', 'signature_image', 'show_map'])) {
                $this->setSetting($key, $value);
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    private function getSettings()
    {
        return [
            'school_name' => $this->getSetting('school_name', 'Shree Education Academy'),
            'school_address' => $this->getSetting('school_address', '123 School Street, Education City - 123456'),
            'school_phone' => $this->getSetting('school_phone', '+91 98765 43210'),
            'school_email' => $this->getSetting('school_email', 'info@shreeeducation.com'),
            'school_website' => $this->getSetting('school_website', 'www.shreeeducation.com'),
            'principal_name' => $this->getSetting('principal_name', 'Dr. Principal Name'),
            'principal_signature' => $this->getSetting('principal_signature', 'Principal'),
            'authorized_signature_text' => $this->getSetting('authorized_signature_text', 'Authorized Signatory'),
            'school_logo' => $this->getSetting('school_logo'),
            'school_favicon' => $this->getSetting('school_favicon'),
            'signature_image' => $this->getSetting('signature_image'),
            'show_map' => $this->getSetting('show_map'),
            'school_map_embed' => $this->getSetting('school_map_embed'),
            'office_hours_school_mf_status' => $this->getSetting('office_hours_school_mf_status'),
            'office_hours_school_mf_open' => $this->getSetting('office_hours_school_mf_open'),
            'office_hours_school_mf_close' => $this->getSetting('office_hours_school_mf_close'),
            'office_hours_school_sat_status' => $this->getSetting('office_hours_school_sat_status'),
            'office_hours_school_sat_open' => $this->getSetting('office_hours_school_sat_open'),
            'office_hours_school_sat_close' => $this->getSetting('office_hours_school_sat_close'),
            'office_hours_school_sun_status' => $this->getSetting('office_hours_school_sun_status'),
            'office_hours_school_sun_open' => $this->getSetting('office_hours_school_sun_open'),
            'office_hours_school_sun_close' => $this->getSetting('office_hours_school_sun_close'),
            'office_hours_admission_mf_status' => $this->getSetting('office_hours_admission_mf_status'),
            'office_hours_admission_mf_open' => $this->getSetting('office_hours_admission_mf_open'),
            'office_hours_admission_mf_close' => $this->getSetting('office_hours_admission_mf_close'),
            'office_hours_admission_sat_status' => $this->getSetting('office_hours_admission_sat_status'),
            'office_hours_admission_sat_open' => $this->getSetting('office_hours_admission_sat_open'),
            'office_hours_admission_sat_close' => $this->getSetting('office_hours_admission_sat_close'),
            'office_hours_admission_sun_status' => $this->getSetting('office_hours_admission_sun_status'),
            'office_hours_admission_sun_open' => $this->getSetting('office_hours_admission_sun_open'),
            'office_hours_admission_sun_close' => $this->getSetting('office_hours_admission_sun_close'),
            'social_facebook' => $this->getSetting('social_facebook'),
            'social_twitter' => $this->getSetting('social_twitter'),
            'social_instagram' => $this->getSetting('social_instagram'),
            'social_youtube' => $this->getSetting('social_youtube'),
            'social_linkedin' => $this->getSetting('social_linkedin'),
            'social_whatsapp' => $this->getSetting('social_whatsapp'),
        ];
    }

    private function getSetting($key, $default = null)
    {
        $settingsFile = storage_path('app/settings.json');
        
        if (!file_exists($settingsFile)) {
            return $default;
        }

        $settings = json_decode(file_get_contents($settingsFile), true);
        return $settings[$key] ?? $default;
    }

    private function setSetting($key, $value)
    {
        // Save to JSON file
        $settingsFile = storage_path('app/settings.json');

        $settings = [];
        if (file_exists($settingsFile)) {
            $settings = json_decode(file_get_contents($settingsFile), true) ?? [];
        }

        $settings[$key] = $value;

        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

        // Also save to database (used by website pages)
        Setting::set($key, $value);
    }

    public static function getSchoolSetting($key, $default = null)
    {
        $settingsFile = storage_path('app/settings.json');
        
        if (!file_exists($settingsFile)) {
            return $default;
        }

        $settings = json_decode(file_get_contents($settingsFile), true);
        return $settings[$key] ?? $default;
    }
}