<?php

namespace App\Notifications;

use App\Models\Homework;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HomeworkAssigned extends Notification
{
	use Queueable;

	protected bool $sendEmail;

	public function __construct(protected Homework $homework, bool $sendEmail = false)
	{
		$this->sendEmail = $sendEmail;
	}

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
			->subject('New Homework: ' . $this->homework->title)
			->view('emails.homework-assigned', [
				'userName' => $notifiable->name,
				'subjectName' => $this->homework->subject->name ?? 'Subject',
				'homeworkTitle' => $this->homework->title,
				'dueDate' => $this->homework->submission_date->format('M d, Y'),
				'description' => $this->homework->description ?? '',
				'actionUrl' => '/portal/homework/' . $this->homework->id,
			]);
	}

	public function toArray($notifiable): array
	{
		return [
			'title' => 'New Homework',
			'message' => ($this->homework->subject->name ?? 'Subject') . ': ' . $this->homework->title . ' (Due: ' . $this->homework->submission_date->format('M d, Y') . ')',
			'icon' => 'book',
			'color' => 'warning',
			'url' => '/portal/homework/' . $this->homework->id,
			'type' => 'homework',
		];
	}
}
