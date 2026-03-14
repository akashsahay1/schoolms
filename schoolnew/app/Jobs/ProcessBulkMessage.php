<?php

namespace App\Jobs;

use App\Models\BulkMessage;
use App\Models\BulkMessageLog;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessBulkMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 600;

    public function __construct(protected BulkMessage $bulkMessage, protected array $recipientIds)
    {
    }

    public function handle(): void
    {
        $recipients = User::whereIn('id', $this->recipientIds)->get();
        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            $channels = $this->getChannels($this->bulkMessage->message_type);

            foreach ($channels as $channel) {
                $log = BulkMessageLog::create([
                    'bulk_message_id' => $this->bulkMessage->id,
                    'user_id' => $recipient->id ?? null,
                    'recipient_name' => $recipient->name ?? $recipient->first_name . ' ' . ($recipient->last_name ?? ''),
                    'recipient_phone' => $recipient->phone ?? $recipient->mobile ?? null,
                    'recipient_email' => $recipient->email ?? null,
                    'channel' => $channel,
                    'status' => BulkMessageLog::STATUS_PENDING,
                ]);

                $success = $this->sendMessage($log, $this->bulkMessage->message, $channel, $this->bulkMessage->title);

                if ($success) {
                    $log->update([
                        'status' => BulkMessageLog::STATUS_SENT,
                        'sent_at' => now(),
                    ]);
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }
        }

        $this->bulkMessage->update([
            'status' => BulkMessage::STATUS_COMPLETED,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'sent_at' => now(),
        ]);
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
                    $log->update(['error_message' => 'SMS gateway not configured', 'status' => BulkMessageLog::STATUS_FAILED]);
                    return false;

                case BulkMessageLog::CHANNEL_EMAIL:
                    if (empty($log->recipient_email)) {
                        $log->update(['error_message' => 'No email address', 'status' => BulkMessageLog::STATUS_FAILED]);
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
                    $log->update(['error_message' => 'No linked user account', 'status' => BulkMessageLog::STATUS_FAILED]);
                    return false;
            }
            return false;
        } catch (\Exception $e) {
            $log->update(['error_message' => $e->getMessage(), 'status' => BulkMessageLog::STATUS_FAILED]);
            return false;
        }
    }
}
