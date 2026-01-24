<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessagingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get conversations (messages grouped by other party)
        $query = Message::where(function ($q) use ($user) {
            $q->where('recipient_id', $user->id)
                ->orWhere('sender_id', $user->id);
        })
        ->whereNull('parent_message_id')
        ->with(['sender', 'recipient', 'student', 'replies'])
        ->latest();

        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'unread':
                    $query->where('recipient_id', $user->id)->unread();
                    break;
                case 'sent':
                    $query->where('sender_id', $user->id);
                    break;
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                    ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $messages = $query->paginate(20);

        $unreadCount = Message::where('recipient_id', $user->id)->unread()->count();

        return view('admin.messaging.inbox.index', compact('messages', 'unreadCount'));
    }

    public function create(Request $request)
    {
        // Get all users who can receive messages (teachers, parents, staff)
        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        })->where('status', 'active')->orderBy('name')->get();

        $parents = User::whereHas('roles', function ($query) {
            $query->where('name', 'parent');
        })->where('status', 'active')->orderBy('name')->get();

        $staff = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'staff', 'accountant', 'librarian']);
        })->where('status', 'active')->orderBy('name')->get();

        $students = Student::where('status', 'active')->orderBy('first_name')->get();

        $replyTo = null;
        if ($request->filled('reply_to')) {
            $replyTo = Message::find($request->reply_to);
        }

        return view('admin.messaging.inbox.create', compact('teachers', 'parents', 'staff', 'students', 'replyTo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'student_id' => 'nullable|exists:students,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'parent_message_id' => 'nullable|exists:messages,id',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('messages', 'public');
        }

        $recipient = User::find($validated['recipient_id']);
        $sender = auth()->user();

        Message::create([
            'sender_id' => $sender->id,
            'sender_type' => $sender->roles->first()?->name ?? 'user',
            'recipient_id' => $validated['recipient_id'],
            'recipient_type' => $recipient->roles->first()?->name ?? 'user',
            'student_id' => $validated['student_id'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'attachment' => $attachmentPath,
            'parent_message_id' => $validated['parent_message_id'] ?? null,
        ]);

        return redirect()->route('admin.messaging.inbox.index')
            ->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        $user = auth()->user();

        // Check if user is part of this conversation
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'You are not authorized to view this message.');
        }

        // Mark as read if recipient
        if ($message->recipient_id === $user->id) {
            $message->markAsRead();
        }

        $message->load(['sender', 'recipient', 'student', 'replies.sender', 'replies.recipient']);

        return view('admin.messaging.inbox.show', compact('message'));
    }

    public function reply(Request $request, Message $message)
    {
        $user = auth()->user();

        // Check if user is part of this conversation
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'You are not authorized to reply to this message.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('messages', 'public');
        }

        // Determine recipient (the other party)
        $recipientId = $message->sender_id === $user->id ? $message->recipient_id : $message->sender_id;
        $recipient = User::find($recipientId);

        Message::create([
            'sender_id' => $user->id,
            'sender_type' => $user->roles->first()?->name ?? 'user',
            'recipient_id' => $recipientId,
            'recipient_type' => $recipient->roles->first()?->name ?? 'user',
            'student_id' => $message->student_id,
            'subject' => 'Re: ' . $message->subject,
            'message' => $validated['message'],
            'attachment' => $attachmentPath,
            'parent_message_id' => $message->id,
        ]);

        return redirect()->route('admin.messaging.inbox.show', $message)
            ->with('success', 'Reply sent successfully.');
    }

    public function destroy(Message $message)
    {
        $user = auth()->user();

        // Check if user is part of this conversation
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'You are not authorized to delete this message.');
        }

        // Delete attachment if exists
        if ($message->attachment) {
            Storage::disk('public')->delete($message->attachment);
        }

        // Delete replies
        foreach ($message->replies as $reply) {
            if ($reply->attachment) {
                Storage::disk('public')->delete($reply->attachment);
            }
            $reply->delete();
        }

        $message->delete();

        return redirect()->route('admin.messaging.inbox.index')
            ->with('success', 'Message deleted successfully.');
    }

    public function markAsRead(Message $message)
    {
        if ($message->recipient_id === auth()->id()) {
            $message->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Message::where('recipient_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->back()->with('success', 'All messages marked as read.');
    }
}
