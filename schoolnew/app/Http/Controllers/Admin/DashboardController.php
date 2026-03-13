<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Staff;
use App\Models\User;
use App\Models\ParentGuardian;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Notice;
use App\Models\Event;
use App\Models\FeeCollection;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\FeeStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $userRole = $this->getPrimaryRole($user);

        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        // Base stats available to all roles
        $totalStudents = Student::where('status', 'active')->count() ?: Student::count();

        // Initialize all variables with defaults
        $stats = [];
        $genderStats = ['male' => 0, 'female' => 0, 'other' => 0];
        $recentStudents = collect([]);
        $classWiseStudents = collect([]);
        $topStudents = collect([]);
        $topPerformers = collect([]);
        $unpaidFees = collect([]);
        $notices = collect([]);
        $upcomingEvents = collect([]);
        $tasks = collect([]);
        $totalTasks = 0;
        $completedTasks = 0;
        $attendanceStats = ['present' => 85, 'absent' => 10, 'late' => 5, 'present_count' => 0, 'absent_count' => 0];
        $pendingLeaves = collect([]);
        $pendingLeavesCount = 0;
        $libraryStats = [];
        $communicationStats = [];

        // Load data based on role
        if (in_array($userRole, ['Super Admin', 'Admin'])) {
            $this->loadFullDashboardData(
                $currentAcademicYear, $totalStudents,
                $stats, $genderStats, $recentStudents, $classWiseStudents,
                $topStudents, $topPerformers, $unpaidFees, $notices, $upcomingEvents,
                $tasks, $totalTasks, $completedTasks, $attendanceStats,
                $pendingLeaves, $pendingLeavesCount
            );
        } elseif ($userRole === 'Accountant') {
            $this->loadAccountantDashboardData(
                $currentAcademicYear, $totalStudents,
                $stats, $unpaidFees
            );
            // Accountant also sees notices
            $notices = $this->loadNotices();
        } elseif ($userRole === 'Librarian') {
            $this->loadLibrarianDashboardData($stats, $libraryStats);
            $notices = $this->loadNotices();
        } elseif ($userRole === 'Receptionist') {
            $this->loadReceptionistDashboardData(
                $totalStudents, $stats, $notices, $upcomingEvents, $communicationStats
            );
        }

        return view('admin.dashboard', compact(
            'userRole',
            'stats',
            'genderStats',
            'recentStudents',
            'classWiseStudents',
            'currentAcademicYear',
            'topStudents',
            'topPerformers',
            'unpaidFees',
            'notices',
            'upcomingEvents',
            'tasks',
            'totalTasks',
            'completedTasks',
            'attendanceStats',
            'pendingLeaves',
            'pendingLeavesCount',
            'libraryStats',
            'communicationStats'
        ));
    }

    /**
     * Get the primary role of the user.
     */
    protected function getPrimaryRole($user): string
    {
        $roleOrder = ['Super Admin', 'Admin', 'Accountant', 'Librarian', 'Receptionist'];
        foreach ($roleOrder as $role) {
            if ($user->hasRole($role)) {
                return $role;
            }
        }
        return $user->getRoleNames()->first() ?? 'Admin';
    }

    /**
     * Load full dashboard data for Super Admin and Admin.
     */
    protected function loadFullDashboardData(
        $currentAcademicYear, $totalStudents,
        &$stats, &$genderStats, &$recentStudents, &$classWiseStudents,
        &$topStudents, &$topPerformers, &$unpaidFees, &$notices, &$upcomingEvents,
        &$tasks, &$totalTasks, &$completedTasks, &$attendanceStats,
        &$pendingLeaves, &$pendingLeavesCount
    ) {
        $totalTeachers = Staff::where('status', 'active')->teachers()->count() ?: Staff::teachers()->count();
        $totalFeeCollected = class_exists(FeeCollection::class) ? (FeeCollection::sum('paid_amount') ?? 0) : 0;
        $totalStaff = Staff::where('status', 'active')->count() ?: Staff::count();

        $stats = [
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_parents' => ParentGuardian::count(),
            'total_classes' => SchoolClass::where('is_active', true)->count() ?: SchoolClass::count(),
            'total_sections' => Section::where('is_active', true)->count() ?: Section::count(),
            'total_subjects' => Subject::where('is_active', true)->count() ?: Subject::count(),
            'total_staff' => $totalStaff,
            'total_income' => $totalFeeCollected,
            'total_expense' => 0,
            'total_revenue' => $totalFeeCollected,
            'homework_completion' => 89,
            'test_average' => 95,
            'attendance_rate' => 92,
        ];

        // Gender distribution
        $maleCount = Student::where('status', 'active')->where('gender', 'male')->count();
        $femaleCount = Student::where('status', 'active')->where('gender', 'female')->count();
        $otherCount = Student::where('status', 'active')->where('gender', 'other')->count();
        if ($maleCount == 0 && $femaleCount == 0 && $otherCount == 0) {
            $maleCount = Student::where('gender', 'male')->count();
            $femaleCount = Student::where('gender', 'female')->count();
            $otherCount = Student::where('gender', 'other')->count();
        }
        $genderStats = ['male' => $maleCount, 'female' => $femaleCount, 'other' => $otherCount];

        $recentStudents = Student::with(['schoolClass', 'section'])->latest()->take(5)->get();
        $classWiseStudents = SchoolClass::with('sections')->withCount('students')->orderBy('order')->get();
        $topStudents = collect([]);
        $topPerformers = collect([]);

        // Unpaid fees
        $unpaidFees = $this->loadUnpaidFees($currentAcademicYear);

        // Notices & Events
        $notices = $this->loadNotices();
        $upcomingEvents = $this->loadUpcomingEvents();

        // Tasks
        $tasks = collect([]);
        $totalTasks = 0;
        $completedTasks = 0;

        // Attendance stats
        if (class_exists(Attendance::class)) {
            $today = now()->toDateString();
            $attendanceStats['present_count'] = Attendance::where('attendance_date', $today)->where('status', 'present')->count();
            $attendanceStats['absent_count'] = Attendance::where('attendance_date', $today)->where('status', 'absent')->count();
            $totalAttendance = $attendanceStats['present_count'] + $attendanceStats['absent_count'];
            if ($totalAttendance > 0) {
                $attendanceStats['present'] = round(($attendanceStats['present_count'] / $totalAttendance) * 100);
                $attendanceStats['absent'] = round(($attendanceStats['absent_count'] / $totalAttendance) * 100);
            }
        }

        // Pending leaves
        if (class_exists(LeaveApplication::class)) {
            $pendingLeavesCount = LeaveApplication::pending()->count();
            $pendingLeaves = LeaveApplication::with(['student.schoolClass', 'student.section'])
                ->pending()->latest()->take(5)->get();
        }
    }

    /**
     * Load accountant-specific dashboard data.
     */
    protected function loadAccountantDashboardData($currentAcademicYear, $totalStudents, &$stats, &$unpaidFees)
    {
        $totalFeeCollected = class_exists(FeeCollection::class) ? (FeeCollection::sum('paid_amount') ?? 0) : 0;
        $todayCollection = class_exists(FeeCollection::class) ? (FeeCollection::whereDate('payment_date', today())->sum('paid_amount') ?? 0) : 0;
        $monthCollection = class_exists(FeeCollection::class) ? (FeeCollection::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('paid_amount') ?? 0) : 0;

        $stats = [
            'total_students' => $totalStudents,
            'total_income' => $totalFeeCollected,
            'total_expense' => 0,
            'total_revenue' => $totalFeeCollected,
            'today_collection' => $todayCollection,
            'month_collection' => $monthCollection,
        ];

        $unpaidFees = $this->loadUnpaidFees($currentAcademicYear);
    }

    /**
     * Load librarian-specific dashboard data.
     */
    protected function loadLibrarianDashboardData(&$stats, &$libraryStats)
    {
        $totalBooks = 0;
        $issuedBooks = 0;
        $availableBooks = 0;
        $overdueBooks = 0;

        if (class_exists(\App\Models\Book::class)) {
            $totalBooks = \App\Models\Book::count();
            $availableBooks = \App\Models\Book::where('available_copies', '>', 0)->count();
        }

        if (class_exists(\App\Models\BookIssue::class)) {
            $issuedBooks = \App\Models\BookIssue::where('status', 'issued')->count();
            $overdueBooks = \App\Models\BookIssue::where('status', 'issued')
                ->where('due_date', '<', now())->count();
        }

        $stats = [
            'total_books' => $totalBooks,
            'issued_books' => $issuedBooks,
            'available_books' => $availableBooks,
            'overdue_books' => $overdueBooks,
        ];

        $libraryStats = $stats;
    }

    /**
     * Load receptionist-specific dashboard data.
     */
    protected function loadReceptionistDashboardData($totalStudents, &$stats, &$notices, &$upcomingEvents, &$communicationStats)
    {
        $stats = [
            'total_students' => $totalStudents,
        ];

        $notices = $this->loadNotices();
        $upcomingEvents = $this->loadUpcomingEvents();

        $totalNotices = class_exists(Notice::class) ? Notice::where('is_published', true)->count() : 0;
        $totalEvents = class_exists(Event::class) ? Event::count() : 0;

        $communicationStats = [
            'total_notices' => $totalNotices,
            'total_events' => $totalEvents,
        ];
    }

    /**
     * Load unpaid fees data.
     */
    protected function loadUnpaidFees($currentAcademicYear)
    {
        $unpaidFees = collect([]);
        $today = now();

        if (!$currentAcademicYear) return $unpaidFees;

        $studentsWithFees = Student::with(['schoolClass', 'section'])
            ->where('status', 'active')
            ->get();

        foreach ($studentsWithFees as $student) {
            $feeStructures = FeeStructure::where('class_id', $student->class_id)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->where('is_active', true)
                ->get();

            if ($feeStructures->isEmpty()) continue;

            $paidFeeStructureIds = FeeCollection::where('student_id', $student->id)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->pluck('fee_structure_id')
                ->toArray();

            $paidAmount = FeeCollection::where('student_id', $student->id)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->sum('paid_amount');

            $totalFees = 0;
            $totalFine = 0;
            $nearestDueDate = null;

            foreach ($feeStructures as $structure) {
                $totalFees += $structure->amount;

                if (!in_array($structure->id, $paidFeeStructureIds) && $structure->due_date && $structure->due_date < $today) {
                    if ($structure->fine_type === 'percentage') {
                        $totalFine += ($structure->amount * $structure->fine_amount) / 100;
                    } else {
                        $totalFine += $structure->fine_amount ?? 0;
                    }
                }

                if ($structure->due_date && (!$nearestDueDate || $structure->due_date < $nearestDueDate)) {
                    $nearestDueDate = $structure->due_date;
                }
            }

            $pendingAmount = ($totalFees + $totalFine) - $paidAmount;

            if ($pendingAmount > 0) {
                $unpaidFees->push((object)[
                    'student' => $student,
                    'total_fees' => $totalFees,
                    'fine_amount' => $totalFine,
                    'paid_amount' => $paidAmount,
                    'pending_amount' => $pendingAmount,
                    'pending_count' => $feeStructures->count() - count($paidFeeStructureIds),
                    'due_date' => $nearestDueDate,
                    'is_overdue' => $nearestDueDate && $nearestDueDate < $today,
                ]);
            }
        }

        return $unpaidFees->sortByDesc('pending_amount')->take(10)->values();
    }

    /**
     * Load notices.
     */
    protected function loadNotices()
    {
        if (!class_exists(Notice::class)) return collect([]);

        return Notice::with('creator')
            ->where('is_published', true)
            ->where('publish_date', '<=', now())
            ->where(function($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('publish_date', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Load upcoming events.
     */
    protected function loadUpcomingEvents()
    {
        if (!class_exists(Event::class)) return collect([]);

        return Event::where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();
    }

    /**
     * Get student statistics for chart (AJAX endpoint)
     */
    public function studentStats(Request $request)
    {
        $query = Student::where('status', 'active');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $maleCount = (clone $query)->where('gender', 'male')->count();
        $femaleCount = (clone $query)->where('gender', 'female')->count();
        $otherCount = (clone $query)->where('gender', 'other')->count();
        $total = $maleCount + $femaleCount + $otherCount;

        $series = [];
        $labels = [];
        $colors = [];

        if ($maleCount > 0 || $total === 0) {
            $series[] = $maleCount;
            $labels[] = 'Boys';
            $colors[] = '#7366FF';
        }
        if ($femaleCount > 0 || $total === 0) {
            $series[] = $femaleCount;
            $labels[] = 'Girls';
            $colors[] = '#ffb829';
        }
        if ($otherCount > 0) {
            $series[] = $otherCount;
            $labels[] = 'Other';
            $colors[] = '#54BA4A';
        }

        if (empty($series)) {
            $series = [0, 0];
            $labels = ['Boys', 'Girls'];
            $colors = ['#7366FF', '#ffb829'];
        }

        return response()->json([
            'series' => $series,
            'labels' => $labels,
            'colors' => $colors,
            'total' => $total
        ]);
    }
}
