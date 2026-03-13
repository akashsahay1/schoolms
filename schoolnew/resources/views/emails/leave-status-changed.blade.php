@extends('emails.layout')

@section('icon', {{ $status === 'approved' ? "'✅'" : "'❌'" }})
@section('heading', 'Leave Application ' . ucfirst($status))

@section('content')
<p class="greeting">Dear {{ $userName }},</p>

<p>
	Your leave application has been
	<span class="badge badge-{{ $status === 'approved' ? 'success' : 'danger' }}">{{ ucfirst($status) }}</span>
</p>

<table class="details-table">
	<tr>
		<td>From</td>
		<td>{{ $fromDate }}</td>
	</tr>
	<tr>
		<td>To</td>
		<td>{{ $toDate }}</td>
	</tr>
</table>

@if($status === 'approved')
	<p>Your leave has been approved. Please ensure necessary arrangements are made before your leave period.</p>
@else
	<p>Unfortunately, your leave could not be approved. Please contact the administration for further details.</p>
@endif
@endsection
