<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ParentGuardian;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // Check if user is a student or parent and redirect accordingly
        $user = Auth::user();

        // Check if user is linked to a student
        $student = Student::where('user_id', $user->id)->first();
        if ($student) {
            return redirect()->intended(route('portal.dashboard'));
        }

        // Check if user is linked to a parent (by user_id or email)
        $parent = ParentGuardian::where('user_id', $user->id)
            ->orWhere('father_email', $user->email)
            ->orWhere('mother_email', $user->email)
            ->orWhere('guardian_email', $user->email)
            ->first();
        if ($parent) {
            return redirect()->intended(route('portal.dashboard'));
        }

        // Check if user is linked to staff (teacher/non-teaching staff)
        $staff = Staff::where('user_id', $user->id)->first();
        if ($staff) {
            // Check user roles - if they have admin panel roles, go to admin dashboard
            if ($user->hasAnyRole(['Super Admin', 'Admin', 'Accountant', 'Librarian', 'Receptionist'])) {
                return redirect()->intended(route('admin.dashboard'));
            }
            // Otherwise go to teacher/staff portal
            return redirect()->intended(route('teacher.dashboard'));
        }

        // Default to admin dashboard for admin users
        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
