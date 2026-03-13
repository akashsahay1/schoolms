<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeeUpdated extends Notification
{
	use Queueable;

	public function __construct(
		protected string $title,
		protected string $message,
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
			->subject($this->title)
			->view('emails.fee-updated', [
				'title' => $this->title,
				'messageText' => $this->message,
				'userName' => $notifiable->name,
			]);
	}

	public function toArray($notifiable): array
	{
		return [
			'title' => $this->title,
			'message' => $this->message,
			'icon' => 'dollar-sign',
			'color' => 'info',
			'url' => '/portal/fees/overview',
			'type' => 'fee',
		];
	}
}
