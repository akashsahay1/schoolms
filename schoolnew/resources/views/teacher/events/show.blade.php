@extends('layouts.teacher-portal')

@section('title', $event->title)
@section('page-title', 'Event Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.events') }}">Events</a></li>
<li class="breadcrumb-item active">View Event</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="card">
			@if($event->image)
				<img src="{{ asset('storage/' . $event->image) }}" class="card-img-top" alt="{{ $event->title }}" style="max-height: 300px; object-fit: cover;">
			@endif
			<div class="card-body">
				<h4 class="mb-3">{{ $event->title }}</h4>
				<div class="row mb-4">
					<div class="col-md-6 mb-3">
						<div class="d-flex align-items-center">
							<div class="quick-action-icon bg-primary bg-opacity-10 me-3">
								<i data-feather="calendar" class="text-primary"></i>
							</div>
							<div>
								<small class="text-muted d-block">Date & Time</small>
								<strong>{{ $event->start_date->format('M d, Y') }}</strong>
								@if($event->start_date->format('Y-m-d') != $event->end_date->format('Y-m-d'))
									- {{ $event->end_date->format('M d, Y') }}
								@endif
							</div>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<div class="d-flex align-items-center">
							<div class="quick-action-icon bg-success bg-opacity-10 me-3">
								<i data-feather="map-pin" class="text-success"></i>
							</div>
							<div>
								<small class="text-muted d-block">Location</small>
								<strong>{{ $event->location ?? 'To be announced' }}</strong>
							</div>
						</div>
					</div>
				</div>
				<hr>
				<div class="event-description">
					{!! nl2br(e($event->description)) !!}
				</div>
			</div>
			<div class="card-footer">
				<a href="{{ route('teacher.events') }}" class="btn btn-secondary">
					<i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Events
				</a>
			</div>
		</div>
	</div>
</div>
@endsection
