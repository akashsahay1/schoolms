<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Display the contact form and message history.
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Get previous messages
        $messages = ContactMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = ContactMessage::CATEGORIES;

        return view('portal.contact', compact('student', 'messages', 'categories'));
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'category' => 'required|in:' . implode(',', array_keys(ContactMessage::CATEGORIES)),
            'priority' => 'required|in:low,medium,high',
        ]);

        ContactMessage::create([
            'user_id' => $user->id,
            'student_id' => $student?->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        return redirect()->route('portal.contact')
            ->with('success', 'Your message has been sent successfully. We will respond soon.');
    }

    /**
     * Display a specific message thread.
     */
    public function show(ContactMessage $message)
    {
        $user = Auth::user();

        // Security check
        if ($message->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $student = Student::where('user_id', $user->id)->first();

        return view('portal.contact-show', compact('student', 'message'));
    }
}
