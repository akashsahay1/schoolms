<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Traits\PortalStudentTrait;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
