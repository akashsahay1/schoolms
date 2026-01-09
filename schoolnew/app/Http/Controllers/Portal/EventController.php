<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display event calendar.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;
        $audience = $student ? 'students' : 'parents';

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Get events for the month (include both specific audience and general events)
        $events = Event::where(function ($q) use ($audience) {
                $q->whereNull('target_audience')
                    ->orWhereJsonContains('target_audience', 'all')
                    ->orWhereJsonContains('target_audience', $audience);
            })
            ->inMonth($year, $month)
            ->orderBy('start_date')
            ->get();

        // Build calendar data
        $calendarData = $this->buildCalendarData($year, $month, $events);

        // Get upcoming events
        $upcomingEvents = Event::where(function ($q) use ($audience) {
                $q->whereNull('target_audience')
                    ->orWhereJsonContains('target_audience', 'all')
                    ->orWhereJsonContains('target_audience', $audience);
            })
            ->upcoming()
            ->orderBy('start_date')
            ->take(10)
            ->get();

        return view('portal.events', compact(
            'student',
            'events',
            'calendarData',
            'upcomingEvents',
            'month',
            'year'
        ));
    }

    /**
     * Display a single event.
     */
    public function show(Event $event)
    {
        $event->load('photos');

        return view('portal.event-show', compact('event'));
    }

    /**
     * Get events for calendar (AJAX).
     */
    public function calendarEvents(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;
        $audience = $student ? 'students' : 'parents';

        $start = $request->get('start');
        $end = $request->get('end');

        $events = Event::where(function ($q) use ($audience) {
                $q->whereNull('target_audience')
                    ->orWhereJsonContains('target_audience', 'all')
                    ->orWhereJsonContains('target_audience', $audience);
            })
            ->whereBetween('start_date', [$start, $end])
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_date->format('Y-m-d'),
                    'end' => $event->end_date ? $event->end_date->format('Y-m-d') : null,
                    'color' => $event->color,
                    'url' => route('portal.events.show', $event),
                ];
            });

        return response()->json($events);
    }

    /**
     * Build calendar data for the month.
     */
    private function buildCalendarData($year, $month, $events)
    {
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $eventsByDate = $events->groupBy(function ($event) {
            return $event->start_date->format('Y-m-d');
        });

        $calendar = [];
        $currentDate = $startDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);

        while ($currentDate <= $endDate->copy()->endOfWeek(\Carbon\Carbon::SATURDAY)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $currentDate->format('Y-m-d');
                $dayData = [
                    'date' => $currentDate->copy(),
                    'day' => $currentDate->day,
                    'inMonth' => $currentDate->month == $month,
                    'isToday' => $currentDate->isToday(),
                    'isSunday' => $currentDate->isSunday(),
                    'events' => $eventsByDate->get($dateKey, collect()),
                ];
                $week[] = $dayData;
                $currentDate->addDay();
            }
            $calendar[] = $week;

            if ($currentDate->month > $month && $currentDate->year >= $year) {
                break;
            }
        }

        return $calendar;
    }
}
