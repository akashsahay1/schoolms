@extends('layouts.teacher-portal')

@section('title', 'Events')
@section('page-title', 'Events')

@section('breadcrumb')
<li class="breadcrumb-item active">Events</li>
@endsection

@section('content')
<div class="row">
	<!-- Upcoming Events -->
	<div class="col-lg-8 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h5 class="mb-0">Upcoming Events</h5>
			</div>
			<div class="card-body">
				@if($upcomingEvents->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($upcomingEvents as $event)
							<a href="{{ route('teacher.events.show', $event) }}" class="list-group-item list-group-item-action">
								<div class="d-flex">
									<div class="flex-shrink-0 me-3 text-center" style="width: 60px;">
										<div class="bg-primary text-white rounded p-2">
											<span class="d-block fs-4 fw-bold">{{ $event->start_date->format('d') }}</span>
											<small>{{ $event->start_date->format('M') }}</small>
										</div>
									</div>
									<div class="flex-grow-1">
										<h6 class="mb-1">{{ $event->title }}</h6>
										<p class="text-muted mb-1 small">{{ Str::limit($event->description, 100) }}</p>
										<small class="text-muted">
											<i data-feather="map-pin" style="width: 12px; height: 12px;"></i>
											{{ $event->location ?? 'TBD' }}
										</small>
									</div>
								</div>
							</a>
						@endforeach
					</div>
				@else
					<div class="text-center py-4">
						<i data-feather="calendar" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
						<p class="text-muted mb-0">No upcoming events</p>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Past Events -->
	<div class="col-lg-4 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h5 class="mb-0">Past Events</h5>
			</div>
			<div class="card-body">
				@if($pastEvents->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($pastEvents as $event)
							<a href="{{ route('teacher.events.show', $event) }}" class="list-group-item list-group-item-action py-2">
								<div class="d-flex justify-content-between align-items-center">
									<span>{{ Str::limit($event->title, 25) }}</span>
									<small class="text-muted">{{ $event->start_date->format('M d') }}</small>
								</div>
							</a>
						@endforeach
					</div>
				@else
					<p class="text-muted text-center mb-0">No past events</p>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
