<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected function getStaff()
    {
        return Staff::where('user_id', Auth::id())->first();
    }

    /**
     * List events for staff.
     */
    public function index()
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $upcomingEvents = Event::upcoming()
            ->where(function ($q) {
                $q->whereNull('target_audience')
                    ->orWhereJsonContains('target_audience', 'all')
                    ->orWhereJsonContains('target_audience', 'staff')
                    ->orWhereJsonContains('target_audience', 'teachers');
            })
            ->orderBy('start_date')
            ->get();

        $pastEvents = Event::where('end_date', '<', now())
            ->where(function ($q) {
                $q->whereNull('target_audience')
                    ->orWhereJsonContains('target_audience', 'all')
                    ->orWhereJsonContains('target_audience', 'staff')
                    ->orWhereJsonContains('target_audience', 'teachers');
            })
            ->orderBy('start_date', 'desc')
            ->take(10)
            ->get();

        return view('teacher.events.index', compact('staff', 'upcomingEvents', 'pastEvents'));
    }

    /**
     * View a single event.
     */
    public function show(Event $event)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Check if event is for staff
        $audience = $event->target_audience ?? [];
        if (!array_intersect($audience, ['all', 'staff', 'teachers'])) {
            return redirect()->route('teacher.events')->with('error', 'Event not found.');
        }

        return view('teacher.events.show', compact('staff', 'event'));
    }
}
