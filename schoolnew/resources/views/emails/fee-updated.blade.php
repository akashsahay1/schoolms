@extends('emails.layout')

@section('icon', '💰')
@section('heading', $title)

@section('content')
<p class="greeting">Dear {{ $userName }},</p>

<div class="message-box">
	<p style="margin: 0;">{{ $messageText }}</p>
</div>

<center>
	<a href="{{ url('/portal/fees/overview') }}" class="btn">View Fee Details</a>
</center>
@endsection
