@extends('emails.layout')

@section('icon', {{ $status === 'present' ? "'✅'" : ($status === 'absent' ? "'❌'" : "'⚠️'") }})
@section('heading', 'Attendance Update')

@section('content')
<p class="greeting">Dear {{ $userName }},</p>

<p>Attendance has been recorded for your class:</p>

<table class="details-table">
	<tr>
		<td>Date</td>
		<td>{{ $date }}</td>
	</tr>
	<tr>
		<td>Class</td>
		<td>{{ $className }} - {{ $sectionName }}</td>
	</tr>
	<tr>
		<td>Status</td>
		<td><span class="badge badge-{{ $status === 'present' ? 'success' : ($status === 'absent' ? 'danger' : 'warning') }}">{{ ucfirst($status) }}</span></td>
	</tr>
</table>

<center>
	<a href="{{ url('/portal/attendance') }}" class="btn">View Attendance</a>
</center>
@endsection
