<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Traits\PortalStudentTrait;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    use PortalStudentTrait;

    /**
     * Display the student profile.
     */
    public function index()
    {
        $user = Auth::user();
        $student = $this->getCurrentStudent();

        if (!$student) {
            return redirect()->route('admin.dashboard');
        }

        // Load additional relationships
        $student->load(['schoolClass', 'section', 'parent', 'academicYear']);

        $isParent = $this->isParentUser();

        return view('portal.profile', compact('student', 'user', 'isParent'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
            'plain_password' => $validated['password'],
        ]);

        return redirect()->route('portal.profile')
            ->with('success', 'Password updated successfully!');
    }
}
