<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Staff;
use App\Models\ParentGuardian;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        // Statistics
        $stats = [
            'total_students' => Student::where('status', 'active')->count(),
            'total_teachers' => Staff::whereHas('designation', function($q) {
                $q->whereIn('name', ['Teacher', 'Senior Teacher', 'Assistant Teacher', 'Head Teacher']);
            })->where('status', 'active')->count(),
            'total_parents' => ParentGuardian::count(),
            'total_classes' => SchoolClass::where('is_active', true)->count(),
            'total_sections' => Section::where('is_active', true)->count(),
            'total_subjects' => Subject::where('is_active', true)->count(),
            'total_staff' => Staff::where('status', 'active')->count(),
            // Finance stats (placeholder - connect to actual finance module when available)
            'total_income' => 0,
            'total_expense' => 0,
            'total_revenue' => 0,
            // Performance stats (placeholder - connect to actual data when available)
            'homework_completion' => 89,
            'test_average' => 95,
            'attendance_rate' => 92,
        ];

        // Gender distribution for students
        $genderStats = [
            'male' => Student::where('status', 'active')->where('gender', 'male')->count(),
            'female' => Student::where('status', 'active')->where('gender', 'female')->count(),
            'other' => Student::where('status', 'active')->where('gender', 'other')->count(),
        ];

        // Recent students
        $recentStudents = Student::with(['schoolClass', 'section'])
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        // Class-wise student count (with sections for dropdown)
        $classWiseStudents = SchoolClass::with(['sections' => function($q) {
            $q->where('is_active', true);
        }])
        ->withCount(['students' => function($q) {
            $q->where('status', 'active');
        }])
        ->where('is_active', true)
        ->orderBy('order')
        ->get();

        // Top students (placeholder - will be populated when exam results are added)
        $topStudents = collect([]);

        // Top performers for table (placeholder)
        $topPerformers = collect([]);

        // Unpaid fees (placeholder - connect to fees module when available)
        $unpaidFees = collect([]);

        // Notices (placeholder - connect to notice module when available)
        $notices = collect([]);

        // Tasks (placeholder - connect to task module when available)
        $tasks = collect([]);
        $totalTasks = 0;
        $completedTasks = 0;

        // Attendance stats (placeholder - connect to attendance module when available)
        $attendanceStats = [
            'present' => 85,
            'absent' => 10,
            'late' => 5,
            'present_count' => 0,
            'absent_count' => 0,
        ];

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
