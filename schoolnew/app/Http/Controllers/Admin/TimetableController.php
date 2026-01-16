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

        // Check room conflict
        if (!empty($validated['room_number'])) {
            $roomConflict = Timetable::where('academic_year_id', $activeYear->id)
                ->where('room_number', $validated['room_number'])
                ->where('day', $validated['day'])
                ->where('period_id', $validated['period_id'])
                ->where('is_active', true)
                ->first();

            if ($roomConflict) {
                $conflictClass = SchoolClass::find($roomConflict->class_id);
                $conflictSection = Section::find($roomConflict->section_id);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Room ' . $validated['room_number'] . ' is already assigned to ' .
                        ($conflictClass->name ?? '') . ' - ' . ($conflictSection->name ?? '') . ' during this period.');
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

    /**
     * Print timetable for a class/section.
     */
    public function print(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $activeYear = AcademicYear::getActive();
        $selectedClass = SchoolClass::find($request->class_id);
        $selectedSection = Section::find($request->section_id);
        $periods = TimetablePeriod::active()->ordered()->get();
        $days = Timetable::getDays();

        $timetableData = collect();
        if ($activeYear) {
            $timetableData = Timetable::with(['subject', 'teacher', 'period'])
                ->forCurrentAcademicYear()
                ->forClassSection($request->class_id, $request->section_id)
                ->active()
                ->get()
                ->groupBy('day');
        }

        return view('admin.timetable.print', compact(
            'selectedClass',
            'selectedSection',
            'timetableData',
            'periods',
            'days',
            'activeYear'
        ));
    }

    /**
     * Display teacher's timetable.
     */
    public function teacherTimetable(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $teachers = Staff::teachers()->active()->orderBy('first_name')->get();

        $timetableData = collect();
        $periods = collect();
        $selectedTeacher = null;

        if ($request->filled('teacher_id')) {
            $selectedTeacher = Staff::find($request->teacher_id);
            $periods = TimetablePeriod::active()->ordered()->get();

            if ($activeYear && $selectedTeacher) {
                $timetableData = Timetable::with(['subject', 'schoolClass', 'section', 'period'])
                    ->where('academic_year_id', $activeYear->id)
                    ->where('teacher_id', $selectedTeacher->id)
                    ->where('is_active', true)
                    ->get()
                    ->groupBy('day');
            }
        }

        $days = Timetable::getDays();

        return view('admin.timetable.teacher', compact(
            'teachers',
            'timetableData',
            'periods',
            'days',
            'activeYear',
            'selectedTeacher'
        ));
    }

    /**
     * Print teacher's timetable.
     */
    public function printTeacherTimetable(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:staff,id',
        ]);

        $activeYear = AcademicYear::getActive();
        $selectedTeacher = Staff::find($request->teacher_id);
        $periods = TimetablePeriod::active()->ordered()->get();
        $days = Timetable::getDays();

        $timetableData = collect();
        if ($activeYear && $selectedTeacher) {
            $timetableData = Timetable::with(['subject', 'schoolClass', 'section', 'period'])
                ->where('academic_year_id', $activeYear->id)
                ->where('teacher_id', $selectedTeacher->id)
                ->where('is_active', true)
                ->get()
                ->groupBy('day');
        }

        return view('admin.timetable.print-teacher', compact(
            'selectedTeacher',
            'timetableData',
            'periods',
            'days',
            'activeYear'
        ));
    }

    /**
     * Get room availability.
     */
    public function getRoomAvailability(Request $request)
    {
        $request->validate([
            'day' => 'required|string',
            'period_id' => 'required|exists:timetable_periods,id',
        ]);

        $activeYear = AcademicYear::getActive();

        if (!$activeYear) {
            return response()->json(['rooms' => []]);
        }

        // Get all rooms that are occupied for this day and period
        $occupiedRooms = Timetable::where('academic_year_id', $activeYear->id)
            ->where('day', $request->day)
            ->where('period_id', $request->period_id)
            ->where('is_active', true)
            ->whereNotNull('room_number')
            ->pluck('room_number')
            ->toArray();

        return response()->json(['occupied_rooms' => $occupiedRooms]);
    }

    /**
     * Get all conflicts for the current academic year.
     */
    public function conflicts()
    {
        $activeYear = AcademicYear::getActive();
        $conflicts = collect();

        if ($activeYear) {
            // Find teacher conflicts (same teacher, same day, same period, multiple classes)
            $teacherConflicts = DB::table('timetables as t1')
                ->join('timetables as t2', function ($join) use ($activeYear) {
                    $join->on('t1.teacher_id', '=', 't2.teacher_id')
                        ->on('t1.day', '=', 't2.day')
                        ->on('t1.period_id', '=', 't2.period_id')
                        ->where('t1.academic_year_id', $activeYear->id)
                        ->where('t2.academic_year_id', $activeYear->id)
                        ->where('t1.is_active', true)
                        ->where('t2.is_active', true)
                        ->whereRaw('t1.id < t2.id');
                })
                ->join('staff', 't1.teacher_id', '=', 'staff.id')
                ->join('classes as c1', 't1.class_id', '=', 'c1.id')
                ->join('classes as c2', 't2.class_id', '=', 'c2.id')
                ->join('timetable_periods', 't1.period_id', '=', 'timetable_periods.id')
                ->select(
                    't1.day',
                    'timetable_periods.name as period_name',
                    'staff.first_name as teacher_name',
                    'staff.last_name as teacher_last_name',
                    'c1.name as class1',
                    'c2.name as class2'
                )
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'Teacher Conflict',
                        'description' => "{$item->teacher_name} {$item->teacher_last_name} is assigned to {$item->class1} and {$item->class2}",
                        'day' => ucfirst($item->day),
                        'period' => $item->period_name,
                    ];
                });

            // Find room conflicts
            $roomConflicts = DB::table('timetables as t1')
                ->join('timetables as t2', function ($join) use ($activeYear) {
                    $join->on('t1.room_number', '=', 't2.room_number')
                        ->on('t1.day', '=', 't2.day')
                        ->on('t1.period_id', '=', 't2.period_id')
                        ->where('t1.academic_year_id', $activeYear->id)
                        ->where('t2.academic_year_id', $activeYear->id)
                        ->where('t1.is_active', true)
                        ->where('t2.is_active', true)
                        ->whereNotNull('t1.room_number')
                        ->whereRaw('t1.id < t2.id');
                })
                ->join('classes as c1', 't1.class_id', '=', 'c1.id')
                ->join('classes as c2', 't2.class_id', '=', 'c2.id')
                ->join('timetable_periods', 't1.period_id', '=', 'timetable_periods.id')
                ->select(
                    't1.day',
                    't1.room_number',
                    'timetable_periods.name as period_name',
                    'c1.name as class1',
                    'c2.name as class2'
                )
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'Room Conflict',
                        'description' => "Room {$item->room_number} is assigned to {$item->class1} and {$item->class2}",
                        'day' => ucfirst($item->day),
                        'period' => $item->period_name,
                    ];
                });

            $conflicts = $teacherConflicts->merge($roomConflicts);
        }

        return view('admin.timetable.conflicts', compact('conflicts', 'activeYear'));
    }
}
