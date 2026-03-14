<?php

namespace App\Notifications;

use App\Models\Notice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoticeEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(protected Notice $notice)
    {
    }

    public function via($notifiable): array
    {
        if (empty($notifiable->email)) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = $this->getUrl($notifiable);

        return (new MailMessage)
            ->subject('New Notice: ' . $this->notice->title)
            ->view('emails.notice-published', [
                'notice' => $this->notice,
                'userName' => $notifiable->name,
                'actionUrl' => $url,
            ]);
    }

    private function getUrl($notifiable): string
    {
        if ($notifiable->hasRole(['Super Admin', 'Admin', 'Accountant', 'Librarian', 'Receptionist'])) {
            return '/admin/notices/' . $this->notice->id;
        } elseif ($notifiable->hasRole('Teacher') || \App\Models\Staff::where('user_id', $notifiable->id)->exists()) {
            return '/teacher/notices/' . $this->notice->id;
        }

        return '/portal/notices/' . $this->notice->id;
    }
}
