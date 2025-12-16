<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature_image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
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

        // Save other settings
        foreach ($validated as $key => $value) {
            if ($key !== 'school_logo' && $key !== 'signature_image') {
                $this->setSetting($key, $value);
            }
        }

        return redirect()->route('admin.settings')
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
            'signature_image' => $this->getSetting('signature_image'),
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
        $settingsFile = storage_path('app/settings.json');
        
        $settings = [];
        if (file_exists($settingsFile)) {
            $settings = json_decode(file_get_contents($settingsFile), true) ?? [];
        }

        $settings[$key] = $value;
        
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
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