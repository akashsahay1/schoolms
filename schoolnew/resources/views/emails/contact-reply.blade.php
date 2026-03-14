@extends('emails.layout')

@section('icon', '💬')
@section('heading', 'Reply to Your Message')

@section('content')
<p class="greeting">Dear {{ $contactName }},</p>

<p>Thank you for reaching out to us. We have reviewed your message and here is our response:</p>

<table class="details-table">
	<tr>
		<td>Your Subject</td>
		<td><strong>{{ $contactSubject }}</strong></td>
	</tr>
</table>

<div class="message-box" style="background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;">
	<p style="margin: 0; color: #666; font-size: 12px;">Your message:</p>
	<p style="margin: 5px 0 0; color: #333;">{{ Str::limit($contactMessage, 200) }}</p>
</div>

<div class="message-box" style="background-color: #e8f5e9; padding: 15px; border-radius: 5px; border-left: 4px solid #4caf50; margin: 15px 0;">
	<p style="margin: 0; color: #666; font-size: 12px;">Our Reply:</p>
	<p style="margin: 5px 0 0; color: #2c323f; line-height: 1.6;">{{ $replyMessage }}</p>
</div>

<p>If you have any further questions, feel free to reach out to us again.</p>

<p>Best regards,<br><strong>{{ $schoolName }}</strong></p>
@endsection
