<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\TimetablePeriod;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Models\Staff;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = SchoolClass::with('sections')->ordered()->get();

        $timetableData = collect();
        $periods = collect();
        $selectedClass = null;
        $selectedSection = null;

        if ($request->filled('class_id') && $request->filled('section_id')) {
            $selectedClass = SchoolClass::find($request->class_id);
            $selectedSection = Section::find($request->section_id);

            $periods = TimetablePeriod::active()->ordered()->get();

            if ($activeYear) {
                $timetableData = Timetable::with(['subject', 'teacher', 'period'])
                    ->forCurrentAcademicYear()
                    ->forClassSection($request->class_id, $request->section_id)
                    ->active()
                    ->get()
                    ->groupBy('day');
            }
        }

        $days = Timetable::getDays();

        return view('admin.timetable.index', compact(
            'classes',
            'timetableData',
            'periods',
            'days',
            'activeYear',
            'selectedClass',
            'selectedSection'
        ));
    }

    public function create(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = SchoolClass::with('sections')->ordered()->get();
        $periods = TimetablePeriod::active()->ordered()->get();
        $days = Timetable::getDays();

        $selectedClass = null;
        $selectedSection = null;
        $subjects = collect();
        $teachers = collect();

        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::with('sections')->find($request->class_id);

            // Get all active subjects (class-subject assignment can be added later)
            $subjects = Subject::active()->ordered()->get();
        }

        if ($request->filled('section_id')) {
            $selectedSection = Section::find($request->section_id);
        }

        // Get teachers from Staff model
        $teachers = Staff::with('designation')
            ->teachers()
            ->active()
            ->orderBy('first_name')
            ->get();

        return view('admin.timetable.create', compact(
            'classes',
            'periods',
            'days',
            'subjects',
            'teachers',
            'activeYear',
            'selectedClass',
            'selectedSection'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'period_id' => 'required|exists:timetable_periods,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:staff,id',
            'room_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $activeYear = AcademicYear::getActive();

        if (!$activeYear) {
            return redirect()->back()->with('error', 'No active academic year found.');
        }

        // Check for conflicts
        $existingSlot = Timetable::where('academic_year_id', $activeYear->id)
            ->where('class_id', $validated['class_id'])
            ->where('section_id', $validated['section_id'])
            ->where('day', $validated['day'])
            ->where('period_id', $validated['period_id'])
            ->where('is_active', true)
            ->first();

        if ($existingSlot) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A timetable entry already exists for this class, section, day, and period.');
        }

        // Check teacher conflict
        if ($validated['teacher_id']) {
            $teacherConflict = Timetable::where('academic_year_id', $activeYear->id)
                ->where('teacher_id', $validated['teacher_id'])
                ->where('day', $validated['day'])
                ->where('period_id', $validated['period_id'])
                ->where('is_active', true)
                ->first();

            if ($teacherConflict) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This teacher is already assigned to another class during this period.');
            }
        }

        $validated['academic_year_id'] = $activeYear->id;
        $validated['is_active'] = true;

        Timetable::create($validated);

        return redirect()->route('admin.timetable.create', [
            'class_id' => $validated['class_id'],
            'section_id' => $validated['section_id']
        ])->with('success', 'Timetable entry added successfully.');
    }

    public function destroy(Timetable $timetable)
    {
        $classId = $timetable->class_id;
        $sectionId = $timetable->section_id;

        $timetable->delete();

        return redirect()->route('admin.timetable.index', [
            'class_id' => $classId,
            'section_id' => $sectionId
        ])->with('success', 'Timetable entry deleted successfully.');
    }

    // Period Management
    public function periods()
    {
        $periods = TimetablePeriod::ordered()->paginate(15);
        return view('admin.timetable.periods', compact('periods'));
    }

    public function createPeriod()
    {
        return view('admin.timetable.create-period');
    }

    public function storePeriod(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:class,break,lunch',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        TimetablePeriod::create($validated);

        return redirect()->route('admin.timetable.periods')
            ->with('success', 'Period created successfully.');
    }

    public function editPeriod(TimetablePeriod $period)
    {
        return view('admin.timetable.edit-period', compact('period'));
    }

    public function updatePeriod(Request $request, TimetablePeriod $period)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:class,break,lunch',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $period->update($validated);

        return redirect()->route('admin.timetable.periods')
            ->with('success', 'Period updated successfully.');
    }

    public function destroyPeriod(TimetablePeriod $period)
    {
        if ($period->timetables()->exists()) {
            return redirect()->route('admin.timetable.periods')
                ->with('error', 'Cannot delete period that is used in timetables.');
        }

        $period->delete();

        return redirect()->route('admin.timetable.periods')
            ->with('success', 'Period deleted successfully.');
    }

    // AJAX endpoints
    public function getSections($classId)
    {
        $sections = Section::where('class_id', $classId)->get(['id', 'name']);
        return response()->json($sections);
    }

    public function getSubjects($classId)
    {
        // Get all active subjects (class-subject assignment can be added later)
        $subjects = Subject::active()->ordered()->get(['id', 'name']);
        return response()->json($subjects);
    }
}
