<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
	use Queueable;

	public function __construct(
		protected string $title,
		protected string $message,
		protected string $icon = 'alert-circle',
		protected string $color = 'primary',
		protected string $url = '#',
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
			->view('emails.general-notification', [
				'title' => $this->title,
				'messageText' => $this->message,
				'userName' => $notifiable->name,
				'actionUrl' => $this->url,
			]);
	}

	public function toArray($notifiable): array
	{
		return [
			'title' => $this->title,
			'message' => $this->message,
			'icon' => $this->icon,
			'color' => $this->color,
			'url' => $this->url,
			'type' => 'general',
		];
	}
}
