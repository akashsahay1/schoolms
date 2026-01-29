<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Traits\PortalStudentTrait;
use App\Models\Student;
use App\Models\Timetable;
use App\Models\TimetablePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimetableController extends Controller
{
    use PortalStudentTrait;

    /**
     * Display the timetable.
     */
    public function index()
    {
        $student = $this->getCurrentStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'No student profile found.');
        }

        // Get all periods
        $periods = TimetablePeriod::orderBy('start_time')->get();

        // Get timetable for student's class and section
        $timetable = Timetable::with(['subject', 'teacher', 'period'])
            ->where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->get()
            ->groupBy('day');

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        return view('portal.timetable', compact('student', 'timetable', 'periods', 'days'));
    }
}
