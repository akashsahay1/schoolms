<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Message;
use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    protected function getStaff()
    {
        return Staff::where('user_id', Auth::id())->first();
    }

    /**
     * List messages (inbox).
     */
    public function index()
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $messages = Message::where('receiver_id', Auth::id())
            ->orWhere('sender_id', Auth::id())
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(15);

        return view('teacher.messages.index', compact('staff', 'messages'));
    }

    /**
     * Show compose form.
     */
    public function compose()
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Get parents of students in teacher's classes
        $myClassIds = Timetable::where('teacher_id', $staff->id)
            ->pluck('class_id')
            ->unique();

        $students = Student::whereIn('class_id', $myClassIds)
            ->where('status', 'active')
            ->with(['parent', 'schoolClass', 'section'])
            ->get();

        return view('teacher.messages.compose', compact('staff', 'students'));
    }

    /**
     * Send a message.
     */
    public function store(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $validated['sender_id'] = Auth::id();
        $validated['sent_at'] = now();

        Message::create($validated);

        return redirect()->route('teacher.messages.index')
            ->with('success', 'Message sent successfully.');
    }

    /**
     * View a message.
     */
    public function show(Message $message)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Check if user is sender or receiver
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            return redirect()->route('teacher.messages.index')->with('error', 'Unauthorized access.');
        }

        // Mark as read if receiver
        if ($message->receiver_id === Auth::id() && !$message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return view('teacher.messages.show', compact('staff', 'message'));
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $message->sender_id,
            'subject' => 'Re: ' . $message->subject,
            'message' => $validated['message'],
            'parent_id' => $message->id,
            'sent_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }
}
