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
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        // Count teachers from User model with Teacher role
        $totalTeachers = User::role('Teacher')->count();

        // Get fee collection stats
        $totalFeeCollected = 0;
        if (class_exists(FeeCollection::class)) {
            $totalFeeCollected = FeeCollection::sum('paid_amount') ?? 0;
        }

        // Statistics - count all records, fallback to counting without status filter
        $totalStudents = Student::where('status', 'active')->count();
        if ($totalStudents == 0) {
            $totalStudents = Student::count(); // Count all if no active status set
        }

        $totalStaff = Staff::where('status', 'active')->count();
        if ($totalStaff == 0) {
            $totalStaff = Staff::count(); // Count all if no active status set
        }

        $stats = [
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_parents' => ParentGuardian::count(),
            'total_classes' => SchoolClass::where('is_active', true)->count() ?: SchoolClass::count(),
            'total_sections' => Section::where('is_active', true)->count() ?: Section::count(),
            'total_subjects' => Subject::where('is_active', true)->count() ?: Subject::count(),
            'total_staff' => $totalStaff,
            // Finance stats
            'total_income' => $totalFeeCollected,
            'total_expense' => 0,
            'total_revenue' => $totalFeeCollected,
            // Performance stats (placeholder - connect to actual data when available)
            'homework_completion' => 89,
            'test_average' => 95,
            'attendance_rate' => 92,
        ];

        // Gender distribution for students
        $maleCount = Student::where('status', 'active')->where('gender', 'male')->count();
        $femaleCount = Student::where('status', 'active')->where('gender', 'female')->count();
        $otherCount = Student::where('status', 'active')->where('gender', 'other')->count();

        // If no active students, count all
        if ($maleCount == 0 && $femaleCount == 0 && $otherCount == 0) {
            $maleCount = Student::where('gender', 'male')->count();
            $femaleCount = Student::where('gender', 'female')->count();
            $otherCount = Student::where('gender', 'other')->count();
        }

        $genderStats = [
            'male' => $maleCount,
            'female' => $femaleCount,
            'other' => $otherCount,
        ];

        // Recent students
        $recentStudents = Student::with(['schoolClass', 'section'])
            ->latest()
            ->take(5)
            ->get();

        // Class-wise student count (with sections for dropdown)
        $classWiseStudents = SchoolClass::with('sections')
            ->withCount('students')
            ->orderBy('order')
            ->get();

        // Top students (placeholder - will be populated when exam results are added)
        $topStudents = collect([]);

        // Top performers for table (placeholder)
        $topPerformers = collect([]);

        // Unpaid fees (placeholder - connect to fees module when available)
        $unpaidFees = collect([]);

        // Fetch notices from Notice model
        $notices = collect([]);
        if (class_exists(Notice::class)) {
            $notices = Notice::with('creator')
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

        // Fetch upcoming events
        $upcomingEvents = collect([]);
        if (class_exists(Event::class)) {
            $upcomingEvents = Event::where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->take(5)
                ->get();
        }

        // Tasks (placeholder - connect to task module when available)
        $tasks = collect([]);
        $totalTasks = 0;
        $completedTasks = 0;

        // Attendance stats
        $attendanceStats = [
            'present' => 85,
            'absent' => 10,
            'late' => 5,
            'present_count' => 0,
            'absent_count' => 0,
        ];

        // Calculate today's attendance if Attendance model exists
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

        return view('admin.dashboard', compact(
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
            'attendanceStats'
        ));
    }

    /**
     * Get student statistics for chart (AJAX endpoint)
     */
    public function studentStats(Request $request)
    {
        $query = Student::where('status', 'active');

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by section
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Get gender counts
        $maleCount = (clone $query)->where('gender', 'male')->count();
        $femaleCount = (clone $query)->where('gender', 'female')->count();
        $otherCount = (clone $query)->where('gender', 'other')->count();
        $total = $maleCount + $femaleCount + $otherCount;

        // Build response
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

        // Default if empty
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
