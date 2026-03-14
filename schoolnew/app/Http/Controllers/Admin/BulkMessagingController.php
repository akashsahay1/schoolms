<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkMessage;
use App\Models\BulkMessageLog;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class BulkMessagingController extends Controller
{
    public function index(Request $request)
    {
        $query = BulkMessage::with('creator')
            ->latest();

        if ($request->filled('status')) {
            $query->status($request->status);
        }

        if ($request->filled('message_type')) {
            $query->where('message_type', $request->message_type);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $messages = $query->paginate(15);

        return view('admin.messaging.bulk.index', compact('messages'));
    }

    public function create()
    {
        $classes = SchoolClass::with('sections')->where('is_active', true)->orderBy('name')->get();
        $messageTypes = BulkMessage::getMessageTypes();
        $recipientTypes = BulkMessage::getRecipientTypes();

        return view('admin.messaging.bulk.create', compact('classes', 'messageTypes', 'recipientTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'message_type' => 'required|in:sms,email,notification,all',
            'recipient_type' => 'required|in:all_students,all_parents,all_teachers,all_staff,class_wise',
            'class_ids' => 'required_if:recipient_type,class_wise|array',
            'class_ids.*' => 'exists:classes,id',
            'section_ids' => 'nullable|array',
            'section_ids.*' => 'exists:sections,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $recipientFilters = null;
        if ($request->recipient_type === 'class_wise') {
            $recipientFilters = [
                'class_ids' => $request->class_ids ?? [],
                'section_ids' => $request->section_ids ?? [],
            ];
        }

        $status = $request->filled('scheduled_at') ? BulkMessage::STATUS_SCHEDULED : BulkMessage::STATUS_DRAFT;

        $bulkMessage = BulkMessage::create([
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'message_type' => $validated['message_type'],
            'recipient_type' => $validated['recipient_type'],
            'recipient_filters' => $recipientFilters,
            'status' => $status,
            'scheduled_at' => $request->scheduled_at,
        ]);

        if ($request->has('send_now')) {
            return $this->processAndSend($bulkMessage);
        }

        return redirect()->route('admin.messaging.bulk.index')
            ->with('success', 'Bulk message created successfully.');
    }

    public function show(BulkMessage $bulkMessage)
    {
        $bulkMessage->load(['creator', 'logs' => function ($query) {
            $query->latest()->limit(100);
        }]);

        $stats = [
            'total' => $bulkMessage->total_recipients,
            'sent' => $bulkMessage->logs()->where('status', 'sent')->count(),
            'delivered' => $bulkMessage->logs()->where('status', 'delivered')->count(),
            'failed' => $bulkMessage->logs()->where('status', 'failed')->count(),
            'pending' => $bulkMessage->logs()->where('status', 'pending')->count(),
        ];

        return view('admin.messaging.bulk.show', compact('bulkMessage', 'stats'));
    }

    public function edit(BulkMessage $bulkMessage)
    {
        if (!in_array($bulkMessage->status, [BulkMessage::STATUS_DRAFT, BulkMessage::STATUS_SCHEDULED])) {
            return redirect()->route('admin.messaging.bulk.index')
                ->with('error', 'Cannot edit a message that is already being sent or completed.');
        }

        $classes = SchoolClass::with('sections')->where('is_active', true)->orderBy('name')->get();
        $messageTypes = BulkMessage::getMessageTypes();
        $recipientTypes = BulkMessage::getRecipientTypes();

        return view('admin.messaging.bulk.edit', compact('bulkMessage', 'classes', 'messageTypes', 'recipientTypes'));
    }

    public function update(Request $request, BulkMessage $bulkMessage)
    {
        if (!in_array($bulkMessage->status, [BulkMessage::STATUS_DRAFT, BulkMessage::STATUS_SCHEDULED])) {
            return redirect()->route('admin.messaging.bulk.index')
                ->with('error', 'Cannot update a message that is already being sent or completed.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'message_type' => 'required|in:sms,email,notification,all',
            'recipient_type' => 'required|in:all_students,all_parents,all_teachers,all_staff,class_wise',
            'class_ids' => 'required_if:recipient_type,class_wise|array',
            'class_ids.*' => 'exists:classes,id',
            'section_ids' => 'nullable|array',
            'section_ids.*' => 'exists:sections,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $recipientFilters = null;
        if ($request->recipient_type === 'class_wise') {
            $recipientFilters = [
                'class_ids' => $request->class_ids ?? [],
                'section_ids' => $request->section_ids ?? [],
            ];
        }

        $status = $request->filled('scheduled_at') ? BulkMessage::STATUS_SCHEDULED : BulkMessage::STATUS_DRAFT;

        $bulkMessage->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'message_type' => $validated['message_type'],
            'recipient_type' => $validated['recipient_type'],
            'recipient_filters' => $recipientFilters,
            'status' => $status,
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()->route('admin.messaging.bulk.index')
            ->with('success', 'Bulk message updated successfully.');
    }

    public function destroy(BulkMessage $bulkMessage)
    {
        if ($bulkMessage->status === BulkMessage::STATUS_SENDING) {
            return redirect()->route('admin.messaging.bulk.index')
                ->with('error', 'Cannot delete a message that is currently being sent.');
        }

        $bulkMessage->logs()->delete();
        $bulkMessage->delete();

        return redirect()->route('admin.messaging.bulk.index')
            ->with('success', 'Bulk message deleted successfully.');
    }

    public function send(BulkMessage $bulkMessage)
    {
        if (!in_array($bulkMessage->status, [BulkMessage::STATUS_DRAFT, BulkMessage::STATUS_SCHEDULED])) {
            return redirect()->route('admin.messaging.bulk.index')
                ->with('error', 'This message cannot be sent.');
        }

        return $this->processAndSend($bulkMessage);
    }

    protected function processAndSend(BulkMessage $bulkMessage)
    {
        $recipients = $this->getRecipients($bulkMessage);

        if ($recipients->isEmpty()) {
            return redirect()->route('admin.messaging.bulk.index')
                ->with('error', 'No recipients found for the selected criteria.');
        }

        $bulkMessage->update([
            'status' => BulkMessage::STATUS_SENDING,
            'total_recipients' => $recipients->count(),
        ]);

        // Dispatch to queue so it processes in background without timeout
        \App\Jobs\ProcessBulkMessage::dispatch($bulkMessage, $recipients->pluck('id')->toArray());

        return redirect()->route('admin.messaging.bulk.show', $bulkMessage)
            ->with('success', "Messages are being sent to {$recipients->count()} recipients in the background. Refresh this page to check progress.");
    }

    protected function getRecipients(BulkMessage $bulkMessage)
    {
        switch ($bulkMessage->recipient_type) {
            case BulkMessage::RECIPIENT_ALL_STUDENTS:
                $studentUserIds = Student::where('status', 'active')->whereNotNull('user_id')->pluck('user_id');
                return User::whereIn('id', $studentUserIds)->get();

            case BulkMessage::RECIPIENT_ALL_PARENTS:
                return User::role('Parent')->get();

            case BulkMessage::RECIPIENT_ALL_TEACHERS:
                return User::role('Teacher')->get();

            case BulkMessage::RECIPIENT_ALL_STAFF:
                return User::role(['Admin', 'Accountant', 'Librarian', 'Receptionist'])->get();

            case BulkMessage::RECIPIENT_CLASS_WISE:
                $filters = $bulkMessage->recipient_filters ?? [];
                $query = Student::where('status', 'active')->whereNotNull('user_id');

                if (!empty($filters['class_ids'])) {
                    $query->whereIn('class_id', $filters['class_ids']);
                }
                if (!empty($filters['section_ids'])) {
                    $query->whereIn('section_id', $filters['section_ids']);
                }

                $studentUserIds = $query->pluck('user_id');
                return User::whereIn('id', $studentUserIds)->get();

            default:
                return collect();
        }
    }

    protected function getChannels(string $messageType): array
    {
        return match ($messageType) {
            BulkMessage::TYPE_SMS => [BulkMessageLog::CHANNEL_SMS],
            BulkMessage::TYPE_EMAIL => [BulkMessageLog::CHANNEL_EMAIL],
            BulkMessage::TYPE_NOTIFICATION => [BulkMessageLog::CHANNEL_NOTIFICATION],
            BulkMessage::TYPE_ALL => [
                BulkMessageLog::CHANNEL_SMS,
                BulkMessageLog::CHANNEL_EMAIL,
                BulkMessageLog::CHANNEL_NOTIFICATION,
            ],
            default => [BulkMessageLog::CHANNEL_NOTIFICATION],
        };
    }

    protected function sendMessage(BulkMessageLog $log, string $message, string $channel, string $title = ''): bool
    {
        try {
            switch ($channel) {
                case BulkMessageLog::CHANNEL_SMS:
                    // SMS gateway not configured - mark as failed
                    $log->update(['error_message' => 'SMS gateway not configured']);
                    return false;

                case BulkMessageLog::CHANNEL_EMAIL:
                    if (empty($log->recipient_email)) {
                        $log->update(['error_message' => 'No email address']);
                        return false;
                    }
                    Mail::raw($message, function ($mail) use ($log, $title) {
                        $mail->to($log->recipient_email)
                             ->subject($title ?: 'Notification from ' . config('app.name'));
                    });
                    return true;

                case BulkMessageLog::CHANNEL_NOTIFICATION:
                    if ($log->user_id) {
                        $user = User::find($log->user_id);
                        if ($user) {
                            $user->notify(new GeneralNotification(
                                $title ?: 'Notification',
                                $message,
                                'message-circle',
                                'info',
                                '#'
                            ));
                            return true;
                        }
                    }
                    $log->update(['error_message' => 'No linked user account']);
                    return false;
            }
            return false;
        } catch (\Exception $e) {
            $log->update(['error_message' => $e->getMessage()]);
            return false;
        }
    }

    public function logs(BulkMessage $bulkMessage, Request $request)
    {
        $query = $bulkMessage->logs()->with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        $logs = $query->latest()->paginate(50);

        return view('admin.messaging.bulk.logs', compact('bulkMessage', 'logs'));
    }
}
