<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

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

        $contactMessage = ContactMessage::create([
            'user_id' => $user->id,
            'student_id' => $student?->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        // Notify admin users about new contact message
        $adminUsers = User::role(['Super Admin', 'Admin'])->get();
        if ($adminUsers->isNotEmpty()) {
            Notification::send($adminUsers, new GeneralNotification(
                'New Contact Message',
                $user->name . ': ' . $validated['subject'],
                'message-circle',
                $validated['priority'] === 'high' ? 'danger' : 'info',
                '/admin/messaging/contact-messages/' . $contactMessage->id
            ));
        }

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
