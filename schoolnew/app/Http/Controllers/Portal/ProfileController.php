<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the student profile.
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['schoolClass', 'section', 'parent', 'academicYear'])
            ->first();

        if (!$student) {
            // Check if parent
            $parent = \App\Models\ParentGuardian::where('user_id', $user->id)
                ->with('students')
                ->first();

            if ($parent) {
                return view('portal.parent-profile', compact('parent', 'user'));
            }

            return redirect()->route('admin.dashboard');
        }

        return view('portal.profile', compact('student', 'user'));
    }
}
