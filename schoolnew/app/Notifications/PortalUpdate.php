<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PortalUpdate extends Notification
{
    use Queueable;

    protected string $module;
    protected string $title;
    protected string $message;

    public function __construct(string $module, string $title, string $message = '')
    {
        $this->module = $module;
        $this->title = $title;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'module' => $this->module, // homework, exams, fees, library, notices
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}
