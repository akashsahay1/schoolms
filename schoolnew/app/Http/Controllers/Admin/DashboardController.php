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
use App\Models\ExamMark;
use App\Models\FeeStructure;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $subjectPerformance = [];
        $unpaidFees = collect([]);
        $notices = collect([]);
        $upcomingEvents = collect([]);
        $tasks = collect([]);
        $totalTasks = 0;
        $completedTasks = 0;
        $attendanceStats = ['present' => 0, 'absent' => 0, 'late' => 0, 'present_count' => 0, 'absent_count' => 0];
        $pendingLeaves = collect([]);
        $pendingLeavesCount = 0;
        $libraryStats = [];
        $communicationStats = [];

        // Load data based on role
        if (in_array($userRole, ['Super Admin', 'Admin'])) {
            $this->loadFullDashboardData(
                $currentAcademicYear, $totalStudents,
                $stats, $genderStats, $recentStudents, $classWiseStudents,
                $topStudents, $topPerformers, $subjectPerformance, $unpaidFees, $notices, $upcomingEvents,
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
            'subjectPerformance',
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
        &$topStudents, &$topPerformers, &$subjectPerformance, &$unpaidFees, &$notices, &$upcomingEvents,
        &$tasks, &$totalTasks, &$completedTasks, &$attendanceStats,
        &$pendingLeaves, &$pendingLeavesCount
    ) {
        $totalTeachers = Staff::where('status', 'active')->teachers()->count() ?: Staff::teachers()->count();
        $totalFeeCollected = class_exists(FeeCollection::class) ? (FeeCollection::sum('paid_amount') ?? 0) : 0;
        $totalStaff = Staff::where('status', 'active')->count() ?: Staff::count();

        // Calculate real homework completion rate
        $homeworkStats = $this->calculateHomeworkStats($currentAcademicYear);
        $testStats = $this->calculateTestStats($currentAcademicYear);
        $attendanceRateStats = $this->calculateAttendanceRateStats();

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
            'homework_completion' => $homeworkStats['rate'],
            'homework_growth' => $homeworkStats['growth'],
            'homework_description' => $homeworkStats['description'],
            'test_average' => $testStats['average'],
            'test_growth' => $testStats['growth'],
            'test_description' => $testStats['description'],
            'attendance_rate' => $attendanceRateStats['rate'],
            'attendance_growth' => $attendanceRateStats['growth'],
            'attendance_description' => $attendanceRateStats['description'],
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

        // Subject-wise performance for polar chart
        $subjectPerformance = [];
        try {
            $subjectData = DB::table('exam_marks')
                ->join('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
                ->where('exam_marks.full_marks', '>', 0)
                ->when($currentAcademicYear, function($q) use ($currentAcademicYear) {
                    $q->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
                      ->where('exams.academic_year_id', $currentAcademicYear->id);
                })
                ->select(
                    'subjects.name',
                    DB::raw('ROUND(AVG(exam_marks.marks_obtained / exam_marks.full_marks * 100)) as avg_pct')
                )
                ->groupBy('subjects.name')
                ->orderByDesc('avg_pct')
                ->limit(6)
                ->get();

            foreach ($subjectData as $row) {
                $subjectPerformance[] = [
                    'name' => $row->name,
                    'value' => (int) $row->avg_pct,
                ];
            }
        } catch (\Exception $e) {
            // Table may not exist
        }

        // Top performers from exam marks
        $topPerformers = collect([]);
        try {
            $topPerformers = DB::table('exam_marks')
                ->join('students', 'exam_marks.student_id', '=', 'students.id')
                ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
                ->where('exam_marks.full_marks', '>', 0)
                ->select(
                    'students.id',
                    'students.admission_no',
                    'students.first_name',
                    'students.last_name',
                    'students.photo',
                    'students.class_id',
                    'students.academic_year_id',
                    'classes.name as class_name',
                    DB::raw('SUM(exam_marks.marks_obtained) as total_marks'),
                    DB::raw('SUM(exam_marks.full_marks) as total_full'),
                    DB::raw('ROUND(SUM(exam_marks.marks_obtained) / SUM(exam_marks.full_marks) * 100, 1) as percentage')
                )
                ->groupBy('students.id', 'students.admission_no', 'students.first_name', 'students.last_name', 'students.photo', 'students.class_id', 'students.academic_year_id', 'classes.name')
                ->orderByDesc('percentage')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            // Table may not exist
        }

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

    /**
     * Academic Performance chart data — average exam marks by day of week.
     */
    public function academicPerformance(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $dates = $this->resolveDateRange($request);
        $period = $request->get('period', 'this_month');

        $cacheKey = 'dash_academic_' . md5($period . ($activeYear?->id ?? 0));
        $cached = cache()->get($cacheKey);
        if ($cached) return response()->json($cached);

        // Always use 6 fixed categories: Mon–Sat
        $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $dayMap = [2 => 0, 3 => 1, 4 => 2, 5 => 3, 6 => 4, 7 => 5]; // MySQL DAYOFWEEK → index

        // Pre-fill with zeros
        $values = array_fill(0, 6, 0);

        $query = ExamMark::query()->where('full_marks', '>', 0);

        if ($activeYear) {
            $hasData = (clone $query)
                ->whereHas('exam', fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->whereBetween('created_at', [$dates['start'], $dates['end']])
                ->exists();
            if ($hasData) {
                $query->whereHas('exam', fn($q) => $q->where('academic_year_id', $activeYear->id));
            }
        }

        $data = $query->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->selectRaw('DAYOFWEEK(created_at) as dow, AVG(marks_obtained / full_marks * 100) as avg_pct')
            ->groupBy('dow')
            ->get();

        // Fill actual values into fixed slots
        foreach ($data as $row) {
            $idx = $dayMap[$row->dow] ?? null;
            if ($idx !== null) {
                $values[$idx] = round($row->avg_pct, 1);
            }
        }

        $result = [
            'categories' => $dayNames,
            'series' => [['name' => 'Avg Score %', 'data' => $values]],
            'has_data' => true,
        ];

        if ($data->isNotEmpty()) {
            cache()->put($cacheKey, $result, 60);
        }

        return response()->json($result);
    }

    /**
     * School Performance chart data — attendance % and exam performance % by week.
     */
    public function schoolPerformance(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $dates = $this->resolveDateRange($request);
        $start = $dates['start'];
        $end = $dates['end'];

        $cacheKey = 'dash_school_' . md5($period);
        $cached = cache()->get($cacheKey);
        if ($cached) return response()->json($cached);

        // Fixed label sets per period — always same count, never single point
        switch ($period) {
            case 'today':
                $labels = ['8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM'];
                $slotCount = 9;
                $groupBy = 'HOUR(attendance_date)';
                $slotMap = [8=>0, 9=>1, 10=>2, 11=>3, 12=>4, 13=>5, 14=>6, 15=>7, 16=>8];
                break;
            case 'this_week':
                $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                $slotCount = 6;
                $groupBy = 'DAYOFWEEK(attendance_date)';
                $slotMap = [2=>0, 3=>1, 4=>2, 5=>3, 6=>4, 7=>5]; // MySQL: Mon=2..Sat=7
                break;
            case 'last_3_months':
                $labels = [];
                $slotMap = [];
                $now = now();
                for ($i = 2; $i >= 0; $i--) {
                    $m = $now->copy()->subMonths($i);
                    $labels[] = $m->format('M');
                    $slotMap[$m->month] = 2 - $i;
                }
                $slotCount = 3;
                $groupBy = 'MONTH(attendance_date)';
                break;
            case 'this_month':
            default:
                $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                $slotCount = 4;
                $groupBy = 'FLOOR((DAY(attendance_date) - 1) / 7) + 1';
                $slotMap = [1=>0, 2=>1, 3=>2, 4=>3];
                break;
        }

        // Pre-fill arrays with zeros
        $attendanceSeries = array_fill(0, $slotCount, 0);
        $examSeries = array_fill(0, $slotCount, 0);

        // Fetch attendance grouped by slot
        $attendanceData = Attendance::whereBetween('attendance_date', [$start, $end])
            ->selectRaw($groupBy . ' as slot_key, COUNT(*) as total, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count')
            ->groupByRaw($groupBy)
            ->get();

        foreach ($attendanceData as $row) {
            $idx = $slotMap[(int)$row->slot_key] ?? null;
            if ($idx !== null) {
                $attendanceSeries[$idx] = $row->total > 0 ? round(($row->present_count / $row->total) * 100, 1) : 0;
            }
        }

        // Fetch exam performance grouped by same slots
        $examGroupBy = str_replace('attendance_date', 'created_at', $groupBy);
        $examResults = ExamMark::where('full_marks', '>', 0)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw($examGroupBy . ' as slot_key, AVG(marks_obtained / full_marks * 100) as avg_pct')
            ->groupByRaw($examGroupBy)
            ->get();

        foreach ($examResults as $row) {
            $idx = $slotMap[(int)$row->slot_key] ?? null;
            if ($idx !== null) {
                $examSeries[$idx] = round($row->avg_pct, 1);
            }
        }

        $result = [
            'labels' => $labels,
            'series' => [
                ['name' => 'Attendance %', 'type' => 'area', 'data' => $attendanceSeries],
                ['name' => 'Exam Score %', 'type' => 'line', 'data' => $examSeries],
            ],
            'has_data' => true,
        ];

        if ($attendanceData->isNotEmpty() || $examResults->isNotEmpty()) {
            cache()->put($cacheKey, $result, 60);
        }

        return response()->json($result);
    }

    /**
     * Finance chart data — income (fee collections) grouped by period.
     */
    public function financePerformance(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $dates = $this->resolveDateRange($request);
        $start = $dates['start'];
        $end = $dates['end'];

        $cacheKey = 'dash_finance_' . md5($period);
        $cached = cache()->get($cacheKey);
        if ($cached) return response()->json($cached);

        // Fixed labels per period
        switch ($period) {
            case 'this_month':
                $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                $groupBy = 'FLOOR((DAY(payment_date) - 1) / 7) + 1';
                $slotMap = [1=>0, 2=>1, 3=>2, 4=>3];
                $slotCount = 4;
                break;
            case 'previous_month':
                $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                $groupBy = 'FLOOR((DAY(payment_date) - 1) / 7) + 1';
                $slotMap = [1=>0, 2=>1, 3=>2, 4=>3];
                $slotCount = 4;
                break;
            case 'last_3_months':
                $labels = [];
                $slotMap = [];
                $now = now();
                for ($i = 2; $i >= 0; $i--) {
                    $m = $now->copy()->subMonths($i);
                    $labels[] = $m->format('M');
                    $slotMap[$m->month] = 2 - $i;
                }
                $groupBy = 'MONTH(payment_date)';
                $slotCount = 3;
                break;
            case 'last_6_months':
            default:
                $labels = [];
                $slotMap = [];
                $now = now();
                for ($i = 5; $i >= 0; $i--) {
                    $m = $now->copy()->subMonths($i);
                    $labels[] = $m->format('M');
                    $slotMap[$m->month] = 5 - $i;
                }
                $groupBy = 'MONTH(payment_date)';
                $slotCount = 6;
                break;
        }

        $incomeSeries = array_fill(0, $slotCount, 0);

        // Fee collections as income
        $incomeData = FeeCollection::whereBetween('payment_date', [$start, $end])
            ->selectRaw($groupBy . ' as slot_key, SUM(paid_amount) as total')
            ->groupByRaw($groupBy)
            ->get();

        $maxVal = 0;
        foreach ($incomeData as $row) {
            $idx = $slotMap[(int)$row->slot_key] ?? null;
            if ($idx !== null) {
                $val = round($row->total / 1000, 1); // Convert to thousands
                $incomeSeries[$idx] = $val;
                if ($val > $maxVal) $maxVal = $val;
            }
        }

        // Revenue = income (same as collected for now)
        $revenueSeries = $incomeSeries;

        // Expense placeholder (zeros — no expense table exists)
        $expenseSeries = array_fill(0, $slotCount, 0);

        $result = [
            'categories' => $labels,
            'series' => [
                ['name' => 'Income', 'type' => 'line', 'data' => $incomeSeries],
                ['name' => 'Expense', 'type' => 'line', 'data' => $expenseSeries],
                ['name' => 'Revenue', 'type' => 'line', 'data' => $revenueSeries],
            ],
            'totals' => [
                'income' => array_sum($incomeSeries),
                'expense' => 0,
                'revenue' => array_sum($revenueSeries),
            ],
            'has_data' => true,
        ];

        if ($incomeData->isNotEmpty()) {
            cache()->put($cacheKey, $result, 60);
        }

        return response()->json($result);
    }

    /**
     * Attendance bar chart data — present vs absent by day.
     */
    public function attendancePerformance(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $dates = $this->resolveDateRange($request);
        $start = $dates['start'];
        $end = $dates['end'];

        $cacheKey = 'dash_attendance_' . md5($period);
        $cached = cache()->get($cacheKey);
        if ($cached) return response()->json($cached);

        // Always 7 days: Mon–Sun
        $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $slotMap = [2=>0, 3=>1, 4=>2, 5=>3, 6=>4, 7=>5, 1=>6]; // MySQL DAYOFWEEK
        $presentSeries = array_fill(0, 7, 0);
        $absentSeries = array_fill(0, 7, 0);

        $data = Attendance::whereBetween('attendance_date', [$start, $end])
            ->selectRaw('DAYOFWEEK(attendance_date) as dow, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count, SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count')
            ->groupByRaw('DAYOFWEEK(attendance_date)')
            ->get();

        foreach ($data as $row) {
            $idx = $slotMap[(int)$row->dow] ?? null;
            if ($idx !== null) {
                $presentSeries[$idx] = (int)$row->present_count;
                $absentSeries[$idx] = (int)$row->absent_count;
            }
        }

        // Calculate percentage stats
        $totalPresent = array_sum($presentSeries);
        $totalAbsent = array_sum($absentSeries);
        $totalAll = $totalPresent + $totalAbsent;
        $presentPct = $totalAll > 0 ? round(($totalPresent / $totalAll) * 100) : 0;
        $absentPct = $totalAll > 0 ? round(($totalAbsent / $totalAll) * 100) : 0;
        $latePct = 0; // No late tracking column yet

        $result = [
            'categories' => $labels,
            'series' => [
                ['name' => 'Total Present', 'data' => $presentSeries],
                ['name' => 'Total Absent', 'data' => $absentSeries],
            ],
            'stats' => [
                'present' => $presentPct,
                'absent' => $absentPct,
                'late' => $latePct,
            ],
            'has_data' => true,
        ];

        if ($data->isNotEmpty()) {
            cache()->put($cacheKey, $result, 60);
        }

        return response()->json($result);
    }

    /**
     * Calculate homework submission/completion stats.
     */
    private function calculateHomeworkStats($currentAcademicYear): array
    {
        $default = ['rate' => 0, 'growth' => 0, 'description' => 'No homework assigned yet'];

        try {
            $query = Homework::where('is_active', true);
            if ($currentAcademicYear) {
                $query->where('academic_year_id', $currentAcademicYear->id);
            }

            $totalHomework = $query->count();
            if ($totalHomework === 0) return $default;

            $homeworkIds = $query->pluck('id');

            // Total expected submissions = homework count × average students per class
            $totalSubmissions = HomeworkSubmission::whereIn('homework_id', $homeworkIds)->count();
            $submittedCount = HomeworkSubmission::whereIn('homework_id', $homeworkIds)->submitted()->count();

            $rate = $totalSubmissions > 0 ? round(($submittedCount / $totalSubmissions) * 100) : 0;

            // Growth: compare this month vs last month
            $thisMonthSubmitted = HomeworkSubmission::whereIn('homework_id', $homeworkIds)
                ->submitted()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            $thisMonthTotal = HomeworkSubmission::whereIn('homework_id', $homeworkIds)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $lastMonthSubmitted = HomeworkSubmission::whereIn('homework_id', $homeworkIds)
                ->submitted()
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();
            $lastMonthTotal = HomeworkSubmission::whereIn('homework_id', $homeworkIds)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();

            $thisMonthRate = $thisMonthTotal > 0 ? ($thisMonthSubmitted / $thisMonthTotal) * 100 : 0;
            $lastMonthRate = $lastMonthTotal > 0 ? ($lastMonthSubmitted / $lastMonthTotal) * 100 : 0;
            $growth = $lastMonthRate > 0 ? round($thisMonthRate - $lastMonthRate) : 0;

            // Latest homework title as description
            $latest = Homework::where('is_active', true)
                ->when($currentAcademicYear, fn($q) => $q->where('academic_year_id', $currentAcademicYear->id))
                ->latest('homework_date')
                ->first();
            $description = $latest ? $latest->title : "{$totalHomework} homework assigned";

            return ['rate' => $rate, 'growth' => $growth, 'description' => $description];
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Calculate test/exam average stats.
     */
    private function calculateTestStats($currentAcademicYear): array
    {
        $default = ['average' => 0, 'growth' => 0, 'description' => 'No exam data available'];

        try {
            $query = ExamMark::where('full_marks', '>', 0);
            if ($currentAcademicYear) {
                $hasData = (clone $query)
                    ->whereHas('exam', fn($q) => $q->where('academic_year_id', $currentAcademicYear->id))
                    ->exists();
                if ($hasData) {
                    $query->whereHas('exam', fn($q) => $q->where('academic_year_id', $currentAcademicYear->id));
                }
            }

            $avgPercentage = $query->selectRaw('AVG(marks_obtained / full_marks * 100) as avg_pct')->value('avg_pct');
            $average = $avgPercentage ? round($avgPercentage) : 0;

            if ($average === 0) return $default;

            // Growth: this month vs last month
            $thisMonthAvg = ExamMark::where('full_marks', '>', 0)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->selectRaw('AVG(marks_obtained / full_marks * 100) as avg_pct')
                ->value('avg_pct') ?? 0;

            $lastMonthAvg = ExamMark::where('full_marks', '>', 0)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->selectRaw('AVG(marks_obtained / full_marks * 100) as avg_pct')
                ->value('avg_pct') ?? 0;

            $growth = $lastMonthAvg > 0 ? round($thisMonthAvg - $lastMonthAvg) : 0;

            $totalExams = ExamMark::when($currentAcademicYear, function($q) use ($currentAcademicYear) {
                $q->whereHas('exam', fn($eq) => $eq->where('academic_year_id', $currentAcademicYear->id));
            })->distinct('exam_id')->count('exam_id');

            $description = "Average across {$totalExams} exam(s)";

            return ['average' => $average, 'growth' => $growth, 'description' => $description];
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Calculate attendance rate stats.
     */
    private function calculateAttendanceRateStats(): array
    {
        $default = ['rate' => 0, 'growth' => 0, 'description' => 'No attendance data available'];

        try {
            $thisMonth = now()->startOfMonth();
            $thisMonthEnd = now()->endOfMonth();

            $totalThisMonth = Attendance::whereBetween('attendance_date', [$thisMonth, $thisMonthEnd])->count();
            $presentThisMonth = Attendance::whereBetween('attendance_date', [$thisMonth, $thisMonthEnd])
                ->where('status', 'present')->count();

            $rate = $totalThisMonth > 0 ? round(($presentThisMonth / $totalThisMonth) * 100) : 0;

            // Growth: compare with last month
            $lastMonth = now()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->subMonth()->endOfMonth();

            $totalLastMonth = Attendance::whereBetween('attendance_date', [$lastMonth, $lastMonthEnd])->count();
            $presentLastMonth = Attendance::whereBetween('attendance_date', [$lastMonth, $lastMonthEnd])
                ->where('status', 'present')->count();

            $lastMonthRate = $totalLastMonth > 0 ? ($presentLastMonth / $totalLastMonth) * 100 : 0;
            $growth = $lastMonthRate > 0 ? round($rate - $lastMonthRate) : 0;

            $description = $totalThisMonth > 0
                ? "{$presentThisMonth} present out of {$totalThisMonth} this month"
                : 'No attendance recorded this month';

            return ['rate' => $rate, 'growth' => $growth, 'description' => $description];
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Resolve date range from request — supports both preset periods and custom dates.
     */
    private function resolveDateRange(Request $request): array
    {
        // Custom date range takes priority
        if ($request->filled('start_date') && $request->filled('end_date')) {
            return [
                'start' => $request->start_date . ' 00:00:00',
                'end' => $request->end_date . ' 23:59:59',
            ];
        }

        $period = $request->get('period', 'this_month');
        $now = now();

        return match($period) {
            'today' => ['start' => $now->copy()->startOfDay(), 'end' => $now->copy()->endOfDay()],
            'this_week' => ['start' => $now->copy()->startOfWeek(), 'end' => $now->copy()->endOfWeek()],
            'previous_month' => ['start' => $now->copy()->subMonth()->startOfMonth(), 'end' => $now->copy()->subMonth()->endOfMonth()],
            'last_3_months' => ['start' => $now->copy()->subMonths(3)->startOfDay(), 'end' => $now->copy()->endOfDay()],
            'last_6_months' => ['start' => $now->copy()->subMonths(6)->startOfDay(), 'end' => $now->copy()->endOfDay()],
            default => ['start' => $now->copy()->startOfMonth(), 'end' => $now->copy()->endOfMonth()],
        };
    }
}
