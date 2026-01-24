<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkMessage;
use App\Models\BulkMessageLog;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'recipient_type' => 'required|in:all_students,all_parents,all_teachers,all_staff,class_wise,custom',
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
            'recipient_type' => 'required|in:all_students,all_parents,all_teachers,all_staff,class_wise,custom',
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

        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            $channels = $this->getChannels($bulkMessage->message_type);

            foreach ($channels as $channel) {
                $log = BulkMessageLog::create([
                    'bulk_message_id' => $bulkMessage->id,
                    'user_id' => $recipient->id ?? null,
                    'recipient_name' => $recipient->name ?? $recipient->first_name . ' ' . ($recipient->last_name ?? ''),
                    'recipient_phone' => $recipient->phone ?? $recipient->mobile ?? null,
                    'recipient_email' => $recipient->email ?? null,
                    'channel' => $channel,
                    'status' => BulkMessageLog::STATUS_PENDING,
                ]);

                // Simulate sending (in production, integrate with actual SMS/Email services)
                $success = $this->sendMessage($log, $bulkMessage->message, $channel);

                if ($success) {
                    $log->update([
                        'status' => BulkMessageLog::STATUS_SENT,
                        'sent_at' => now(),
                    ]);
                    $sentCount++;
                } else {
                    $log->update([
                        'status' => BulkMessageLog::STATUS_FAILED,
                        'error_message' => 'Failed to send message',
                    ]);
                    $failedCount++;
                }
            }
        }

        $bulkMessage->update([
            'status' => BulkMessage::STATUS_COMPLETED,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'sent_at' => now(),
        ]);

        return redirect()->route('admin.messaging.bulk.show', $bulkMessage)
            ->with('success', "Message sent to {$sentCount} recipients. {$failedCount} failed.");
    }

    protected function getRecipients(BulkMessage $bulkMessage)
    {
        switch ($bulkMessage->recipient_type) {
            case BulkMessage::RECIPIENT_ALL_STUDENTS:
                return Student::where('status', 'active')->get();

            case BulkMessage::RECIPIENT_ALL_PARENTS:
                return User::whereHas('roles', function ($query) {
                    $query->where('name', 'parent');
                })->where('status', 'active')->get();

            case BulkMessage::RECIPIENT_ALL_TEACHERS:
                return User::whereHas('roles', function ($query) {
                    $query->where('name', 'teacher');
                })->where('status', 'active')->get();

            case BulkMessage::RECIPIENT_ALL_STAFF:
                return User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['staff', 'admin', 'accountant', 'librarian']);
                })->where('status', 'active')->get();

            case BulkMessage::RECIPIENT_CLASS_WISE:
                $filters = $bulkMessage->recipient_filters ?? [];
                $query = Student::where('status', 'active');

                if (!empty($filters['class_ids'])) {
                    $query->whereIn('class_id', $filters['class_ids']);
                }
                if (!empty($filters['section_ids'])) {
                    $query->whereIn('section_id', $filters['section_ids']);
                }

                return $query->get();

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

    protected function sendMessage(BulkMessageLog $log, string $message, string $channel): bool
    {
        // In production, integrate with actual SMS/Email services
        // For now, simulate successful sending
        try {
            switch ($channel) {
                case BulkMessageLog::CHANNEL_SMS:
                    // Integrate with SMS gateway (e.g., Twilio, MSG91)
                    // Example: SmsService::send($log->recipient_phone, $message);
                    break;

                case BulkMessageLog::CHANNEL_EMAIL:
                    // Send email using Laravel's Mail facade
                    // Example: Mail::to($log->recipient_email)->send(new BulkNotification($message));
                    break;

                case BulkMessageLog::CHANNEL_NOTIFICATION:
                    // Create in-app notification
                    if ($log->user_id) {
                        // Notification::create(['user_id' => $log->user_id, 'message' => $message]);
                    }
                    break;
            }
            return true;
        } catch (\Exception $e) {
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
