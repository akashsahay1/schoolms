<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\FeeCollection;
use App\Models\Notice;
use App\Models\Event;
use App\Models\LeaveApplication;
use App\Models\AcademicYear;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the student/parent dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        // Check if user is a student
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            // Check if user is a parent
            $parent = \App\Models\ParentGuardian::where('user_id', $user->id)->first();

            if ($parent) {
                return $this->parentDashboard($parent, $currentAcademicYear);
            }

            // Neither student nor parent - redirect to admin
            return redirect()->route('admin.dashboard');
        }

        return $this->studentDashboard($student, $currentAcademicYear);
    }

    /**
     * Display student dashboard.
     */
    private function studentDashboard(Student $student, $currentAcademicYear)
    {
        // Attendance stats for current month
        $attendanceStats = $this->getAttendanceStats($student->id);

        // Fee stats
        $feeStats = $this->getFeeStats($student->id);

        // Today's timetable
        $todaysTimetable = Timetable::with(['period', 'subject', 'teacher'])
            ->where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->where('day', strtolower(now()->format('l')))
            ->orderBy('period_id')
            ->get();

        // Recent notices
        $notices = Notice::published()
            ->active()
            ->forAudience('students')
            ->forClass($student->class_id)
            ->latest('publish_date')
            ->take(5)
            ->get();

        // Upcoming events
        $upcomingEvents = Event::upcoming()
            ->forAudience('students')
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Pending leave applications
        $pendingLeaves = LeaveApplication::forStudent($student->id)
            ->pending()
            ->latest()
            ->take(3)
            ->get();

        return view('portal.dashboard', compact(
            'student',
            'currentAcademicYear',
            'attendanceStats',
            'feeStats',
            'todaysTimetable',
            'notices',
            'upcomingEvents',
            'pendingLeaves'
        ));
    }

    /**
     * Display parent dashboard.
     */
    private function parentDashboard($parent, $currentAcademicYear)
    {
        // Get all children
        $children = Student::where('parent_id', $parent->id)
            ->where('status', 'active')
            ->with(['schoolClass', 'section'])
            ->get();

        $childrenStats = [];
        foreach ($children as $child) {
            $childrenStats[$child->id] = [
                'attendance' => $this->getAttendanceStats($child->id),
                'fees' => $this->getFeeStats($child->id),
            ];
        }

        // Recent notices for parents
        $notices = Notice::published()
            ->active()
            ->forAudience('parents')
            ->latest('publish_date')
            ->take(5)
            ->get();

        // Upcoming events
        $upcomingEvents = Event::upcoming()
            ->forAudience('parents')
            ->orderBy('start_date')
            ->take(5)
            ->get();

        return view('portal.parent-dashboard', compact(
            'parent',
            'children',
            'childrenStats',
            'currentAcademicYear',
            'notices',
            'upcomingEvents'
        ));
    }

    /**
     * Get attendance stats for a student.
     */
    private function getAttendanceStats($studentId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $attendance = Attendance::where('student_id', $studentId)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();

        $totalDays = $attendance->count();
        $present = $attendance->where('status', 'present')->count();
        $absent = $attendance->where('status', 'absent')->count();
        $late = $attendance->where('status', 'late')->count();
        $halfDay = $attendance->where('status', 'half_day')->count();

        $percentage = $totalDays > 0 ? round((($present + $halfDay * 0.5) / $totalDays) * 100, 1) : 0;

        return [
            'total' => $totalDays,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'half_day' => $halfDay,
            'percentage' => $percentage,
        ];
    }

    /**
     * Get fee stats for a student.
     */
    private function getFeeStats($studentId)
    {
        $collections = FeeCollection::where('student_id', $studentId)->get();

        $totalPaid = $collections->sum('amount_paid');
        $totalDue = $collections->sum('amount_due');
        $totalDiscount = $collections->sum('discount');

        return [
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'total_discount' => $totalDiscount,
            'pending_count' => $collections->where('status', 'partial')->count() + $collections->where('status', 'pending')->count(),
        ];
    }
}
