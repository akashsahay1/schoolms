<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventPhoto;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request)
    {
        $query = Event::with('creator')->latest('start_date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('start_date', $request->month)
                ->whereYear('start_date', $request->year);
        }

        $events = $query->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('order')->get();
        $types = Event::TYPES;
        $colors = Event::COLORS;

        return view('admin.events.create', compact('classes', 'types', 'colors'));
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', array_keys(Event::TYPES)),
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'is_holiday' => 'boolean',
            'is_public' => 'boolean',
            'target_audience' => 'nullable|array',
            'target_classes' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $academicYear = AcademicYear::where('is_active', true)->first();

        $event = Event::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'venue' => $validated['venue'] ?? null,
            'color' => $validated['color'] ?? Event::COLORS[$validated['type']] ?? '#3498db',
            'is_holiday' => $request->boolean('is_holiday'),
            'is_public' => $request->boolean('is_public', true),
            'target_audience' => !empty($validated['target_audience'] ?? null) ? $validated['target_audience'] : ['all'],
            'target_classes' => $validated['target_classes'] ?? null,
            'image' => $imagePath,
            'created_by' => Auth::id(),
            'academic_year_id' => $academicYear?->id,
        ]);

        // Handle gallery photos
        if ($request->hasFile('photos')) {
            $order = 0;
            foreach ($request->file('photos') as $photo) {
                $photoPath = $photo->store('events/gallery', 'public');
                EventPhoto::create([
                    'event_id' => $event->id,
                    'image' => $photoPath,
                    'sort_order' => $order++,
                ]);
            }
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load('photos');
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the event.
     */
    public function edit(Event $event)
    {
        $event->load('photos');
        $classes = SchoolClass::where('is_active', true)->orderBy('order')->get();
        $types = Event::TYPES;
        $colors = Event::COLORS;

        return view('admin.events.edit', compact('event', 'classes', 'types', 'colors'));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', array_keys(Event::TYPES)),
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'is_holiday' => 'boolean',
            'is_public' => 'boolean',
            'target_audience' => 'nullable|array',
            'target_classes' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = $event->image;
        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'venue' => $validated['venue'] ?? null,
            'color' => $validated['color'] ?? Event::COLORS[$validated['type']] ?? '#3498db',
            'is_holiday' => $request->boolean('is_holiday'),
            'is_public' => $request->boolean('is_public', true),
            'target_audience' => !empty($validated['target_audience'] ?? null) ? $validated['target_audience'] : ['all'],
            'target_classes' => $validated['target_classes'] ?? null,
            'image' => $imagePath,
        ]);

        // Handle new gallery photos
        if ($request->hasFile('photos')) {
            $order = $event->photos()->max('sort_order') ?? 0;
            foreach ($request->file('photos') as $photo) {
                $photoPath = $photo->store('events/gallery', 'public');
                EventPhoto::create([
                    'event_id' => $event->id,
                    'image' => $photoPath,
                    'sort_order' => ++$order,
                ]);
            }
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        // Delete gallery photos
        foreach ($event->photos as $photo) {
            Storage::disk('public')->delete($photo->image);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Delete a specific photo from the gallery.
     */
    public function deletePhoto(EventPhoto $photo)
    {
        Storage::disk('public')->delete($photo->image);
        $photo->delete();

        return back()->with('success', 'Photo deleted successfully.');
    }
}
