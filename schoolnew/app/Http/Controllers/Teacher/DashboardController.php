<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Timetable;
use App\Models\Homework;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\Notice;
use App\Models\Event;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class DashboardController extends Controller
{
    /**
     * Get the authenticated staff member.
     */
    protected function getStaff()
    {
        return Staff::where('user_id', Auth::id())->first();
    }

    /**
     * Display teacher dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $staff = $this->getStaff();

        if (!$staff) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Staff profile not found.');
        }

        // Today's timetable
        $todaysTimetable = Timetable::with(['period', 'subject', 'schoolClass', 'section'])
            ->where('teacher_id', $staff->id)
            ->where('day', strtolower(now()->format('l')))
            ->orderBy('period_id')
            ->get();

        // My classes (unique class-section combinations)
        $myClasses = Timetable::where('teacher_id', $staff->id)
            ->with(['schoolClass', 'section'])
            ->get()
            ->unique(function ($item) {
                return $item->class_id . '-' . $item->section_id;
            });

        // Pending homework to review
        $pendingHomework = Homework::where('teacher_id', $user->id)
            ->where('submission_date', '>=', now()->subDays(7))
            ->withCount(['submissions' => function ($q) {
                $q->where('status', 'submitted');
            }])
            ->having('submissions_count', '>', 0)
            ->latest()
            ->take(5)
            ->get();

        // My leave applications
        $myLeaves = LeaveApplication::where('applicant_type', Staff::class)
            ->where('applicant_id', $staff->id)
            ->latest()
            ->take(3)
            ->get();

        // Recent notices for staff
        $notices = Notice::published()
            ->active()
            ->where(function ($q) {
                $q->whereJsonContains('target_audience', 'all')
                    ->orWhereJsonContains('target_audience', 'staff')
                    ->orWhereJsonContains('target_audience', 'teachers');
            })
            ->latest('publish_date')
            ->take(5)
            ->get();

        // Upcoming events
        $upcomingEvents = Event::upcoming()
            ->where(function ($q) {
                $q->whereNull('target_audience')
                    ->orWhereJsonContains('target_audience', 'all')
                    ->orWhereJsonContains('target_audience', 'staff')
                    ->orWhereJsonContains('target_audience', 'teachers');
            })
            ->orderBy('start_date')
            ->take(3)
            ->get();

        // Stats
        $stats = [
            'total_classes' => $myClasses->count(),
            'total_students' => $this->getMyStudentsCount($staff),
            'pending_reviews' => $pendingHomework->sum('submissions_count'),
            'classes_today' => $todaysTimetable->count(),
        ];

        return view('teacher.dashboard', compact(
            'user',
            'staff',
            'todaysTimetable',
            'myClasses',
            'pendingHomework',
            'myLeaves',
            'notices',
            'upcomingEvents',
            'stats'
        ));
    }

    /**
     * Get total students count for teacher's classes.
     */
    private function getMyStudentsCount($staff)
    {
        $classIds = Timetable::where('teacher_id', $staff->id)
            ->pluck('class_id')
            ->unique();

        return Student::whereIn('class_id', $classIds)
            ->where('status', 'active')
            ->count();
    }

    /**
     * My Timetable
     */
    public function timetable()
    {
        $staff = $this->getStaff();

        if (!$staff) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Staff profile not found.');
        }

        $timetable = Timetable::with(['period', 'subject', 'schoolClass', 'section'])
            ->where('teacher_id', $staff->id)
            ->get()
            ->groupBy('day');

        $periods = \App\Models\TimetablePeriod::orderBy('start_time')->get();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        return view('teacher.timetable', compact('staff', 'timetable', 'periods', 'days'));
    }

    /**
     * My Classes
     */
    public function myClasses()
    {
        $staff = $this->getStaff();

        if (!$staff) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Staff profile not found.');
        }

        $classes = Timetable::where('teacher_id', $staff->id)
            ->with(['schoolClass', 'section', 'subject'])
            ->get()
            ->groupBy(function ($item) {
                return $item->class_id . '-' . $item->section_id;
            })
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'class' => $first->schoolClass,
                    'section' => $first->section,
                    'subjects' => $group->pluck('subject')->unique('id'),
                    'student_count' => Student::where('class_id', $first->class_id)
                        ->where('section_id', $first->section_id)
                        ->where('status', 'active')
                        ->count(),
                ];
            });

        return view('teacher.my-classes', compact('staff', 'classes'));
    }

    /**
     * View students in a class
     */
    public function classStudents($classId, $sectionId)
    {
        $staff = $this->getStaff();

        if (!$staff) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Staff profile not found.');
        }

        $class = SchoolClass::findOrFail($classId);
        $section = \App\Models\Section::findOrFail($sectionId);

        $students = Student::where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('status', 'active')
            ->with('parent')
            ->orderBy('first_name')
            ->get();

        return view('teacher.class-students', compact('staff', 'class', 'section', 'students'));
    }

    /**
     * My Profile
     */
    public function profile()
    {
        $user = Auth::user();
        $staff = $this->getStaff();

        if (!$staff) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Staff profile not found.');
        }

        $staff->load(['department', 'designation']);

        return view('teacher.profile', compact('user', 'staff'));
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

        return redirect()->route('teacher.profile')
            ->with('success', 'Password updated successfully!');
    }
}
