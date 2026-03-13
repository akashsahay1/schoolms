<?php

namespace App\Notifications;

use App\Models\Notice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoticePublished extends Notification
{
	use Queueable;

	protected bool $sendEmail;
	protected bool $emailOnly;

	public function __construct(protected Notice $notice, bool $sendEmail = false, bool $emailOnly = false)
	{
		$this->sendEmail = $sendEmail;
		$this->emailOnly = $emailOnly;
	}

	public function via($notifiable): array
	{
		if ($this->emailOnly) {
			return $this->isMailConfigured() ? ['mail'] : [];
		}

		$channels = ['database'];

		if ($this->sendEmail && $this->isMailConfigured()) {
			$channels[] = 'mail';
		}

		return $channels;
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

	public function toArray($notifiable): array
	{
		return [
			'title' => 'New Notice',
			'message' => $this->notice->title,
			'icon' => 'bell',
			'color' => $this->notice->type === 'urgent' ? 'danger' : 'primary',
			'url' => $this->getUrl($notifiable),
			'type' => 'notice',
		];
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

	private function isMailConfigured(): bool
	{
		return config('mail.default') !== 'log' && !empty(config('mail.mailers.smtp.host'));
	}
}
