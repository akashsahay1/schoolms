<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function school()
    {
        $settings = Setting::getByGroup('school');

        return view('admin.settings.school', compact('settings'));
    }

    public function updateSchool(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string|max:500',
            'school_phone' => 'required|string|max:20',
            'school_email' => 'required|email|max:255',
            'school_website' => 'nullable|string|max:255',
            'school_tagline' => 'nullable|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update text settings
        Setting::set('school_name', $request->school_name);
        Setting::set('school_address', $request->school_address);
        Setting::set('school_phone', $request->school_phone);
        Setting::set('school_email', $request->school_email);
        Setting::set('school_website', $request->school_website);
        Setting::set('school_tagline', $request->school_tagline);

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('school_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Store new logo
            $logoPath = $request->file('school_logo')->store('logos', 'public');
            Setting::set('school_logo', $logoPath);
        }

        // Handle logo removal
        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            $oldLogo = Setting::get('school_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('school_logo', null);
        }

        return redirect()->route('admin.settings.school')
            ->with('success', 'School settings updated successfully!');
    }

    /**
     * Display library settings.
     */
    public function library()
    {
        $settings = Setting::getByGroup('library');

        // Set defaults if not exists
        $defaults = [
            'library_fine_per_day' => 2,
            'library_max_books_per_student' => 3,
            'library_default_issue_days' => 14,
            'library_allow_renewal' => '1',
            'library_max_renewals' => 2,
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($settings[$key])) {
                $settings[$key] = $value;
            }
        }

        return view('admin.settings.library', compact('settings'));
    }

    /**
     * Update library settings.
     */
    public function updateLibrary(Request $request)
    {
        $request->validate([
            'library_fine_per_day' => 'required|numeric|min:0',
            'library_max_books_per_student' => 'required|integer|min:1|max:20',
            'library_default_issue_days' => 'required|integer|min:1|max:90',
            'library_max_renewals' => 'required|integer|min:0|max:10',
        ]);

        Setting::set('library_fine_per_day', $request->library_fine_per_day);
        Setting::set('library_max_books_per_student', $request->library_max_books_per_student);
        Setting::set('library_default_issue_days', $request->library_default_issue_days);
        Setting::set('library_allow_renewal', $request->has('library_allow_renewal') ? '1' : '0');
        Setting::set('library_max_renewals', $request->library_max_renewals);

        // Update group for all library settings
        Setting::where('key', 'like', 'library_%')->update(['group' => 'library']);

        return redirect()->route('admin.settings.library')
            ->with('success', 'Library settings updated successfully!');
    }
}
