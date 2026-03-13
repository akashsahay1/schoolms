<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Message;
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

        $messages = Message::where('recipient_id', Auth::id())
            ->orWhere('sender_id', Auth::id())
            ->with(['sender', 'recipient'])
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
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $validated['sender_id'] = Auth::id();

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

        // Check if user is sender or recipient
        if ($message->sender_id !== Auth::id() && $message->recipient_id !== Auth::id()) {
            return redirect()->route('teacher.messages.index')->with('error', 'Unauthorized access.');
        }

        // Mark as read if recipient
        if ($message->recipient_id === Auth::id() && !$message->is_read) {
            $message->markAsRead();
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
            'recipient_id' => $message->sender_id,
            'subject' => 'Re: ' . $message->subject,
            'message' => $validated['message'],
            'parent_message_id' => $message->id,
        ]);

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }
}
