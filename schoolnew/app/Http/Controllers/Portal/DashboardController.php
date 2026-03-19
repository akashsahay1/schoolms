<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Traits\PortalStudentTrait;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\FeeCollection;
use App\Models\FeeStructure;
use App\Models\Notice;
use App\Models\Event;
use App\Models\LeaveApplication;
use App\Models\AcademicYear;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use PortalStudentTrait;

    /**
     * Switch the selected child for parent users.
     */
    public function switchChild(Request $request)
    {
        $request->validate([
            'child_id' => 'required|integer'
        ]);

        // Use the trait's getParent method which handles email fallback
        $parent = $this->getParent();

        if (!$parent) {
            return response()->json(['success' => false, 'message' => 'Not a parent user']);
        }

        // Verify the child belongs to this parent
        $child = Student::where('id', $request->child_id)
            ->where('parent_id', $parent->id)
            ->where('status', 'active')
            ->first();

        if (!$child) {
            return response()->json(['success' => false, 'message' => 'Invalid child']);
        }

        session(['selected_child_id' => $child->id]);

        return response()->json(['success' => true]);
    }
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
            // Check if user is a parent (uses trait with email fallback)
            $parent = $this->getParent();

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

        // Notification badge counts per module
        $badgeCounts = $this->getPortalBadgeCounts(Auth::user());

        return view('portal.dashboard', compact(
            'student',
            'currentAcademicYear',
            'attendanceStats',
            'feeStats',
            'todaysTimetable',
            'notices',
            'upcomingEvents',
            'pendingLeaves',
            'badgeCounts'
        ));
    }

    /**
     * Get unread notification counts grouped by module.
     */
    private function getPortalBadgeCounts($user): array
    {
        $unread = $user->unreadNotifications()
            ->where('type', 'App\\Notifications\\PortalUpdate')
            ->get();

        $counts = [
            'homework' => 0,
            'exams' => 0,
            'fees' => 0,
            'library' => 0,
            'notices' => 0,
        ];

        foreach ($unread as $notification) {
            $module = $notification->data['module'] ?? '';
            if (isset($counts[$module])) {
                $counts[$module]++;
            }
        }

        return $counts;
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

        $badgeCounts = $this->getPortalBadgeCounts(Auth::user());

        return view('portal.parent-dashboard', compact(
            'parent',
            'children',
            'childrenStats',
            'currentAcademicYear',
            'notices',
            'upcomingEvents',
            'badgeCounts'
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
            ->whereMonth('attendance_date', $currentMonth)
            ->whereYear('attendance_date', $currentYear)
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
     * Get fee stats for a student — same logic as Fee Overview page.
     */
    private function getFeeStats($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return ['total_fees' => 0, 'total_paid' => 0, 'total_due' => 0, 'total_discount' => 0];
        }

        // Get fee structures for student's class
        $feeStructures = FeeStructure::where('class_id', $student->class_id)
            ->where('is_active', true)
            ->get();

        $feeStructureIds = $feeStructures->pluck('id')->toArray();

        // Get fee collections for these structures
        $feeCollections = FeeCollection::where('student_id', $studentId)
            ->whereIn('fee_structure_id', $feeStructureIds)
            ->get();

        // Calculate totals per structure (same as Fee Overview)
        $totalFees = 0;
        $totalPaid = 0;
        $totalDiscount = 0;

        foreach ($feeStructures as $structure) {
            $totalFees += $structure->amount;
            $payments = $feeCollections->where('fee_structure_id', $structure->id);
            $totalPaid += $payments->sum('paid_amount');
            $totalDiscount += $payments->sum('discount_amount');
        }

        $totalDue = $totalFees - $totalPaid - $totalDiscount;

        return [
            'total_fees' => $totalFees,
            'total_paid' => $totalPaid,
            'total_due' => max(0, $totalDue),
            'total_discount' => $totalDiscount,
        ];
    }
}
