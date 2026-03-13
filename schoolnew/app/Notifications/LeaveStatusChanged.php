<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveStatusChanged extends Notification
{
	use Queueable;

	public function __construct(
		protected $leave,
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
		$fromDate = $this->leave->from_date?->format('M d, Y') ?? '';
		$toDate = $this->leave->to_date?->format('M d, Y') ?? '';

		return (new MailMessage)
			->subject('Leave Application ' . ucfirst($this->status))
			->view('emails.leave-status-changed', [
				'userName' => $notifiable->name,
				'status' => $this->status,
				'fromDate' => $fromDate,
				'toDate' => $toDate,
			]);
	}

	public function toArray($notifiable): array
	{
		$fromDate = $this->leave->from_date?->format('M d') ?? '';
		$toDate = $this->leave->to_date?->format('M d, Y') ?? '';

		return [
			'title' => 'Leave ' . ucfirst($this->status),
			'message' => "Your leave application from {$fromDate} to {$toDate} has been {$this->status}",
			'icon' => $this->status === 'approved' ? 'check-circle' : 'x-circle',
			'color' => $this->status === 'approved' ? 'success' : 'danger',
			'url' => '#',
			'type' => 'leave',
		];
	}
}
