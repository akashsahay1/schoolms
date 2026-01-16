<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\PromotionBatch;
use App\Models\PromotionRule;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentPromotion;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Display promotion dashboard.
     */
    public function index()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = SchoolClass::ordered()->get();
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        // Stats
        $stats = [
            'total_promoted' => StudentPromotion::promoted()->count(),
            'total_retained' => StudentPromotion::retained()->count(),
            'total_alumni' => StudentPromotion::alumni()->count(),
            'pending' => StudentPromotion::pending()->count(),
        ];

        // Recent batches
        $recentBatches = PromotionBatch::with(['fromAcademicYear', 'toAcademicYear', 'fromClass', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.promotions.index', compact(
            'academicYears',
            'classes',
            'currentAcademicYear',
            'stats',
            'recentBatches'
        ));
    }

    /**
     * Display promotion rules.
     */
    public function rules()
    {
        $rules = PromotionRule::with(['academicYear', 'schoolClass'])
            ->orderBy('class_id')
            ->get();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = SchoolClass::ordered()->get();

        return view('admin.promotions.rules', compact('rules', 'academicYears', 'classes'));
    }

    /**
     * Store promotion rule.
     */
    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id' => 'required|exists:classes,id',
            'min_attendance_percentage' => 'required|numeric|min:0|max:100',
            'min_marks_percentage' => 'required|numeric|min:0|max:100',
            'consider_attendance' => 'boolean',
            'consider_marks' => 'boolean',
            'auto_promote' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['consider_attendance'] = $request->has('consider_attendance');
        $validated['consider_marks'] = $request->has('consider_marks');
        $validated['auto_promote'] = $request->has('auto_promote');

        PromotionRule::updateOrCreate(
            [
                'academic_year_id' => $validated['academic_year_id'],
                'class_id' => $validated['class_id'],
            ],
            $validated
        );

        return back()->with('success', 'Promotion rule saved successfully!');
    }

    /**
     * Delete promotion rule.
     */
    public function deleteRule(PromotionRule $rule)
    {
        $rule->delete();
        return back()->with('success', 'Promotion rule deleted successfully!');
    }

    /**
     * Display create promotion form.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = SchoolClass::ordered()->get();
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        return view('admin.promotions.create', compact('academicYears', 'classes', 'currentAcademicYear'));
    }

    /**
     * Get sections for a class.
     */
    public function getSections($classId)
    {
        $sections = Section::where('class_id', $classId)->get(['id', 'name']);
        return response()->json($sections);
    }

    /**
     * Get students for promotion preview.
     */
    public function getStudents(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $query = Student::where('class_id', $request->class_id)
            ->where('status', 'active')
            ->with(['schoolClass', 'section']);

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->orderBy('roll_no')->get();

        // Get promotion rule
        $rule = PromotionRule::where('academic_year_id', $request->academic_year_id)
            ->where('class_id', $request->class_id)
            ->first();

        // Get student data with marks and attendance
        $studentsData = [];
        foreach ($students as $student) {
            $attendancePercentage = $this->calculateAttendancePercentage($student->id, $request->academic_year_id);
            $marksPercentage = $this->calculateMarksPercentage($student->id, $request->class_id);

            // Determine promotion eligibility
            $eligible = true;
            $reason = '';

            if ($rule) {
                if ($rule->consider_attendance && $attendancePercentage < $rule->min_attendance_percentage) {
                    $eligible = false;
                    $reason = 'Low attendance (' . $attendancePercentage . '%)';
                }
                if ($rule->consider_marks && $marksPercentage < $rule->min_marks_percentage) {
                    $eligible = false;
                    $reason = $reason ? $reason . ', Low marks (' . $marksPercentage . '%)' : 'Low marks (' . $marksPercentage . '%)';
                }
            }

            $studentsData[] = [
                'id' => $student->id,
                'admission_no' => $student->admission_no,
                'name' => $student->first_name . ' ' . $student->last_name,
                'roll_no' => $student->roll_no,
                'class' => $student->schoolClass->name ?? '-',
                'section' => $student->section->name ?? '-',
                'attendance_percentage' => $attendancePercentage,
                'marks_percentage' => $marksPercentage,
                'eligible' => $eligible,
                'reason' => $reason,
                'photo' => $student->photo ? asset('storage/' . $student->photo) : null,
            ];
        }

        return response()->json([
            'students' => $studentsData,
            'rule' => $rule,
            'total' => count($studentsData),
        ]);
    }

    /**
     * Calculate attendance percentage for a student.
     */
    private function calculateAttendancePercentage($studentId, $academicYearId)
    {
        $attendance = Attendance::where('student_id', $studentId)->get();

        if ($attendance->isEmpty()) {
            return 0;
        }

        $totalDays = $attendance->count();
        $presentDays = $attendance->whereIn('status', ['present', 'late'])->count();
        $halfDays = $attendance->where('status', 'half_day')->count();

        return $totalDays > 0 ? round((($presentDays + $halfDays * 0.5) / $totalDays) * 100, 2) : 0;
    }

    /**
     * Calculate marks percentage for a student.
     */
    private function calculateMarksPercentage($studentId, $classId)
    {
        $marks = ExamMark::where('student_id', $studentId)->get();

        if ($marks->isEmpty()) {
            return 0;
        }

        $totalMarks = $marks->sum('marks_obtained');
        $totalFullMarks = $marks->sum('full_marks');

        return $totalFullMarks > 0 ? round(($totalMarks / $totalFullMarks) * 100, 2) : 0;
    }

    /**
     * Calculate grade based on percentage.
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 33) return 'D';
        return 'F';
    }

    /**
     * Process promotion.
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'from_academic_year_id' => 'required|exists:academic_years,id',
            'to_academic_year_id' => 'required|exists:academic_years,id|different:from_academic_year_id',
            'from_class_id' => 'required|exists:classes,id',
            'to_class_id' => 'required|exists:classes,id',
            'from_section_id' => 'nullable|exists:sections,id',
            'to_section_id' => 'nullable|exists:sections,id',
            'students' => 'required|array',
            'students.*.id' => 'required|exists:students,id',
            'students.*.action' => 'required|in:promote,retain,alumni,skip',
            'students.*.remarks' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Create promotion batch
            $batch = PromotionBatch::create([
                'from_academic_year_id' => $validated['from_academic_year_id'],
                'to_academic_year_id' => $validated['to_academic_year_id'],
                'from_class_id' => $validated['from_class_id'],
                'from_section_id' => $validated['from_section_id'] ?? null,
                'total_students' => count($validated['students']),
                'status' => PromotionBatch::STATUS_PROCESSED,
                'created_by' => Auth::id(),
                'processed_at' => now(),
                'notes' => $validated['notes'],
            ]);

            $promotedCount = 0;
            $retainedCount = 0;
            $alumniCount = 0;

            foreach ($validated['students'] as $studentData) {
                if ($studentData['action'] == 'skip') {
                    continue;
                }

                $student = Student::find($studentData['id']);
                $attendancePercentage = $this->calculateAttendancePercentage($student->id, $validated['from_academic_year_id']);
                $marksPercentage = $this->calculateMarksPercentage($student->id, $validated['from_class_id']);

                $status = match ($studentData['action']) {
                    'promote' => StudentPromotion::STATUS_PROMOTED,
                    'retain' => StudentPromotion::STATUS_RETAINED,
                    'alumni' => StudentPromotion::STATUS_ALUMNI,
                    default => StudentPromotion::STATUS_PENDING,
                };

                // Create promotion record
                StudentPromotion::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'from_academic_year_id' => $validated['from_academic_year_id'],
                    ],
                    [
                        'to_academic_year_id' => $validated['to_academic_year_id'],
                        'from_class_id' => $validated['from_class_id'],
                        'to_class_id' => $studentData['action'] == 'promote' ? $validated['to_class_id'] : ($studentData['action'] == 'retain' ? $validated['from_class_id'] : null),
                        'from_section_id' => $student->section_id,
                        'to_section_id' => $studentData['action'] == 'promote' ? ($validated['to_section_id'] ?? null) : ($studentData['action'] == 'retain' ? $student->section_id : null),
                        'status' => $status,
                        'promotion_type' => StudentPromotion::TYPE_REGULAR,
                        'final_percentage' => $marksPercentage,
                        'attendance_percentage' => $attendancePercentage,
                        'grade' => $this->calculateGrade($marksPercentage),
                        'remarks' => $studentData['remarks'] ?? null,
                        'promoted_by' => Auth::id(),
                        'promoted_at' => now(),
                    ]
                );

                // Update student's class and section if promoted
                if ($studentData['action'] == 'promote') {
                    $student->update([
                        'class_id' => $validated['to_class_id'],
                        'section_id' => $validated['to_section_id'] ?? null,
                    ]);
                    $promotedCount++;
                } elseif ($studentData['action'] == 'alumni') {
                    $student->update(['status' => 'alumni']);
                    $alumniCount++;
                } else {
                    $retainedCount++;
                }
            }

            // Update batch counts
            $batch->update([
                'promoted_count' => $promotedCount,
                'retained_count' => $retainedCount,
                'alumni_count' => $alumniCount,
            ]);

            DB::commit();

            return redirect()->route('admin.promotions.history')
                ->with('success', "Promotion processed successfully! Promoted: {$promotedCount}, Retained: {$retainedCount}, Alumni: {$alumniCount}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process promotion: ' . $e->getMessage());
        }
    }

    /**
     * Display promotion history.
     */
    public function history(Request $request)
    {
        $query = StudentPromotion::with([
            'student',
            'fromAcademicYear',
            'toAcademicYear',
            'fromClass',
            'toClass',
            'fromSection',
            'toSection',
            'promotedBy',
        ]);

        // Filters
        if ($request->filled('academic_year_id')) {
            $query->where('from_academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('class_id')) {
            $query->where('from_class_id', $request->class_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $promotions = $query->orderBy('promoted_at', 'desc')->paginate(20);

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = SchoolClass::ordered()->get();

        return view('admin.promotions.history', compact('promotions', 'academicYears', 'classes'));
    }

    /**
     * Rollback a promotion.
     */
    public function rollback(StudentPromotion $promotion)
    {
        if ($promotion->status == StudentPromotion::STATUS_CANCELLED) {
            return back()->with('error', 'This promotion has already been cancelled.');
        }

        try {
            DB::beginTransaction();

            // Restore student's previous class and section
            $student = $promotion->student;
            $student->update([
                'class_id' => $promotion->from_class_id,
                'section_id' => $promotion->from_section_id,
                'status' => 'active',
            ]);

            // Update promotion status
            $promotion->update([
                'status' => StudentPromotion::STATUS_CANCELLED,
                'remarks' => ($promotion->remarks ? $promotion->remarks . ' | ' : '') . 'Rolled back on ' . now()->format('Y-m-d H:i'),
            ]);

            DB::commit();

            return back()->with('success', 'Promotion rolled back successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to rollback promotion: ' . $e->getMessage());
        }
    }

    /**
     * Finalize a batch.
     */
    public function finalizeBatch(PromotionBatch $batch)
    {
        if ($batch->status == PromotionBatch::STATUS_FINALIZED) {
            return back()->with('error', 'This batch has already been finalized.');
        }

        $batch->update([
            'status' => PromotionBatch::STATUS_FINALIZED,
            'finalized_at' => now(),
        ]);

        return back()->with('success', 'Batch finalized successfully!');
    }
}
