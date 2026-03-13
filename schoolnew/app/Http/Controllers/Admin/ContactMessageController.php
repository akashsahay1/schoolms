<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::with(['user', 'student', 'assignedTo'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $messages = $query->paginate(20);

        $stats = [
            'open' => ContactMessage::where('status', 'open')->count(),
            'in_progress' => ContactMessage::where('status', 'in_progress')->count(),
            'resolved' => ContactMessage::whereIn('status', ['resolved', 'closed'])->count(),
            'total' => ContactMessage::count(),
        ];

        return view('admin.messaging.contact-messages.index', compact('messages', 'stats'));
    }

    public function show(ContactMessage $contactMessage)
    {
        $contactMessage->load(['user', 'student', 'assignedTo', 'respondedBy']);

        // Auto-assign to current admin if not assigned
        if (!$contactMessage->assigned_to && $contactMessage->status === 'open') {
            $contactMessage->update([
                'assigned_to' => Auth::id(),
                'status' => 'in_progress',
            ]);
            $contactMessage->refresh();
        }

        return view('admin.messaging.contact-messages.show', compact('contactMessage'));
    }

    public function respond(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'admin_response' => 'required|string|max:2000',
            'status' => 'required|in:in_progress,resolved,closed',
        ]);

        $contactMessage->update([
            'admin_response' => $validated['admin_response'],
            'status' => $validated['status'],
            'responded_by' => Auth::id(),
            'responded_at' => now(),
        ]);

        // Notify the student/parent that their message was responded to
        if ($contactMessage->user) {
            $contactMessage->user->notify(new GeneralNotification(
                'Message Response',
                'Your message "' . $contactMessage->subject . '" has been responded to.',
                'message-circle',
                'success',
                '/portal/contact/' . $contactMessage->id
            ));
        }

        return redirect()->route('admin.messaging.contact-messages.show', $contactMessage)
            ->with('success', 'Response sent successfully.');
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $contactMessage->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.messaging.contact-messages.index')
            ->with('success', 'Message deleted successfully.');
    }
}
