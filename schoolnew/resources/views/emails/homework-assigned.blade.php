@extends('emails.layout')

@section('icon', '📚')
@section('heading', 'New Homework Assigned')

@section('content')
<p class="greeting">Dear {{ $userName }},</p>

<p>A new homework has been assigned to your class:</p>

<table class="details-table">
	<tr>
		<td>Subject</td>
		<td><strong>{{ $subjectName }}</strong></td>
	</tr>
	<tr>
		<td>Title</td>
		<td>{{ $homeworkTitle }}</td>
	</tr>
	<tr>
		<td>Due Date</td>
		<td><span class="badge badge-warning">{{ $dueDate }}</span></td>
	</tr>
</table>

@if($description)
<div class="message-box">
	<p style="margin: 0;">{{ Str::limit($description, 300) }}</p>
</div>
@endif

<center>
	<a href="{{ url($actionUrl) }}" class="btn">View Homework</a>
</center>
@endsection
