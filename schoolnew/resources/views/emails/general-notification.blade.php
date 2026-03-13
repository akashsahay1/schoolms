@extends('emails.layout')

@section('icon', '🔔')
@section('heading', $title)

@section('content')
<p class="greeting">Dear {{ $userName }},</p>

<div class="message-box">
	<p style="margin: 0;">{{ $messageText }}</p>
</div>

@if($actionUrl && $actionUrl !== '#')
<center>
	<a href="{{ url($actionUrl) }}" class="btn">View Details</a>
</center>
@endif
@endsection
