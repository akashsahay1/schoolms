<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Timetable;
use App\Models\TimetablePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimetableController extends Controller
{
    /**
     * Display the timetable.
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['schoolClass', 'section'])
            ->first();

        if (!$student) {
            return redirect()->route('portal.dashboard');
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
