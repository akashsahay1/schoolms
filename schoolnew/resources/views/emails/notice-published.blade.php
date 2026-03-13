@extends('emails.layout')

@section('icon', '📢')
@section('heading', 'New Notice Published')

@section('content')
<p class="greeting">Dear {{ $userName }},</p>

<p>A new notice has been published by the school administration:</p>

<table class="details-table">
	<tr>
		<td>Title</td>
		<td><strong>{{ $notice->title }}</strong></td>
	</tr>
	<tr>
		<td>Type</td>
		<td><span class="badge badge-{{ $notice->type === 'urgent' ? 'danger' : 'primary' }}">{{ $notice->getTypeLabel() }}</span></td>
	</tr>
	<tr>
		<td>Published</td>
		<td>{{ $notice->publish_date->format('F d, Y') }}</td>
	</tr>
	@if($notice->expiry_date)
	<tr>
		<td>Expires</td>
		<td>{{ $notice->expiry_date->format('F d, Y') }}</td>
	</tr>
	@endif
</table>

<div class="message-box">
	{!! nl2br(e(Str::limit($notice->content, 300))) !!}
</div>

<center>
	<a href="{{ url($actionUrl) }}" class="btn">View Full Notice</a>
</center>
@endsection
