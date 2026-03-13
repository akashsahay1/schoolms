<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceMarked extends Notification
{
	use Queueable;

	public function __construct(
		protected string $className,
		protected string $sectionName,
		protected string $date,
		protected string $status,
		protected bool $sendEmail = false
	) {}

	public function via($notifiable): array
	{
		$channels = ['database'];

		if ($this->sendEmail && config('mail.default') !== 'log' && !empty(config('mail.mailers.smtp.host'))) {
			$channels[] = 'mail';
		}

		return $channels;
	}

	public function toMail($notifiable): MailMessage
	{
		return (new MailMessage)
			->subject('Attendance Update - ' . $this->date)
			->view('emails.attendance-marked', [
				'userName' => $notifiable->name,
				'className' => $this->className,
				'sectionName' => $this->sectionName,
				'date' => $this->date,
				'status' => $this->status,
			]);
	}

	public function toArray($notifiable): array
	{
		return [
			'title' => 'Attendance Update',
			'message' => "Attendance for {$this->date}: " . ucfirst($this->status),
			'icon' => 'check-circle',
			'color' => $this->status === 'present' ? 'success' : ($this->status === 'absent' ? 'danger' : 'warning'),
			'url' => '/portal/attendance',
			'type' => 'attendance',
		];
	}
}
