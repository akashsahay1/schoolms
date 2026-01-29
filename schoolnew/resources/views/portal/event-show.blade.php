@extends('layouts.portal')

@section('title', $event->title)
@section('page-title', 'Event Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('portal.events') }}">Events</a></li>
	<li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="container-fluid">
	<div class="row">
		<!-- Main Event Content -->
		<div class="col-lg-8 mb-4">
			<div class="card">
				@if($event->image)
					<img src="{{ asset('storage/' . $event->image) }}" class="card-img-top" alt="{{ $event->title }}" style="max-height: 300px; object-fit: cover;">
				@endif
				<div class="card-body">
					<!-- Event Type & Holiday Badge -->
					<div class="mb-3 d-flex flex-wrap gap-2">
						<span class="badge py-2 px-3" style="background-color: {{ $event->color }}; color: white;">
							<i data-feather="tag" style="width: 14px; height: 14px;"></i>
							{{ $event->getTypeLabel() }}
						</span>
						@if($event->is_holiday)
							<span class="badge badge-light-danger py-2 px-3">
								<i data-feather="calendar" style="width: 14px; height: 14px;"></i> School Holiday
							</span>
						@endif
					</div>

					<!-- Event Title -->
					<h3 class="mb-4">{{ $event->title }}</h3>

					<!-- Event Details Grid -->
					<div class="row g-3 mb-4">
						<div class="col-md-6">
							<div class="bg-light rounded p-3 h-100">
								<div class="d-flex align-items-center">
									<div class="quick-action-icon bg-primary bg-opacity-10 me-3" style="width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
										<i data-feather="calendar" class="text-primary" style="width: 20px; height: 20px;"></i>
									</div>
									<div>
										<small class="text-muted d-block">Date</small>
										<span class="fw-medium">
											{{ $event->start_date->format('F d, Y') }}
											@if($event->isMultiDay())
												- {{ $event->end_date->format('F d, Y') }}
											@endif
										</span>
									</div>
								</div>
							</div>
						</div>
						@if($event->start_time)
							<div class="col-md-6">
								<div class="bg-light rounded p-3 h-100">
									<div class="d-flex align-items-center">
										<div class="quick-action-icon bg-warning bg-opacity-10 me-3" style="width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
											<i data-feather="clock" class="text-warning" style="width: 20px; height: 20px;"></i>
										</div>
										<div>
											<small class="text-muted d-block">Time</small>
											<span class="fw-medium">
												{{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}
												@if($event->end_time)
													- {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}
												@endif
											</span>
										</div>
									</div>
								</div>
							</div>
						@endif
						@if($event->venue)
							<div class="col-md-6">
								<div class="bg-light rounded p-3 h-100">
									<div class="d-flex align-items-center">
										<div class="quick-action-icon bg-success bg-opacity-10 me-3" style="width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
											<i data-feather="map-pin" class="text-success" style="width: 20px; height: 20px;"></i>
										</div>
										<div>
											<small class="text-muted d-block">Venue</small>
											<span class="fw-medium">{{ $event->venue }}</span>
										</div>
									</div>
								</div>
							</div>
						@endif
						@if($event->isMultiDay())
							<div class="col-md-6">
								<div class="bg-light rounded p-3 h-100">
									<div class="d-flex align-items-center">
										<div class="quick-action-icon bg-info bg-opacity-10 me-3" style="width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
											<i data-feather="hash" class="text-info" style="width: 20px; height: 20px;"></i>
										</div>
										<div>
											<small class="text-muted d-block">Duration</small>
											<span class="fw-medium">{{ $event->getDurationDays() }} days</span>
										</div>
									</div>
								</div>
							</div>
						@endif
					</div>

					<!-- Description -->
					@if($event->description)
						<hr>
						<div class="py-3">
							<h6 class="mb-3">
								<i data-feather="file-text" style="width: 16px; height: 16px;"></i> Description
							</h6>
							<div style="line-height: 1.8;">
								{!! nl2br(e($event->description)) !!}
							</div>
						</div>
					@endif
				</div>
			</div>

			<!-- Photo Gallery -->
			@if($event->photos->count() > 0)
				<div class="card">
					<div class="card-header pb-0">
						<h5 class="mb-0">
							<i data-feather="image" style="width: 18px; height: 18px;"></i> Photo Gallery
						</h5>
					</div>
					<div class="card-body">
						<div class="row g-3">
							@foreach($event->photos as $photo)
								<div class="col-md-4 col-6">
									<a href="{{ $photo->image_url }}" target="_blank" class="d-block">
										<img src="{{ $photo->image_url }}" alt="{{ $photo->caption ?? 'Event Photo' }}" class="img-fluid rounded shadow-sm" style="width: 100%; height: 150px; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
									</a>
									@if($photo->caption)
										<small class="text-muted d-block mt-1">{{ $photo->caption }}</small>
									@endif
								</div>
							@endforeach
						</div>
					</div>
				</div>
			@endif
		</div>

		<!-- Sidebar -->
		<div class="col-lg-4 mb-4">
			<!-- Date Card -->
			<div class="card mb-3">
				<div class="card-body text-center">
					<div class="bg-primary bg-opacity-10 rounded p-4 mb-3">
						<h1 class="display-3 text-primary mb-0 fw-bold">{{ $event->start_date->format('d') }}</h1>
						<h5 class="text-primary mb-0">{{ $event->start_date->format('F Y') }}</h5>
					</div>
					<div class="d-flex align-items-center justify-content-center text-muted">
						<i data-feather="clock" class="me-2" style="width: 16px; height: 16px;"></i>
						{{ $event->start_date->diffForHumans() }}
					</div>
				</div>
			</div>

			<!-- Quick Info -->
			<div class="card mb-3">
				<div class="card-header pb-0">
					<h6 class="mb-0">
						<i data-feather="info" style="width: 16px; height: 16px;"></i> Quick Info
					</h6>
				</div>
				<div class="card-body">
					<ul class="list-unstyled mb-0">
						<li class="mb-2 d-flex justify-content-between">
							<span class="text-muted">Event Type:</span>
							<span class="fw-medium">{{ $event->getTypeLabel() }}</span>
						</li>
						@if($event->isMultiDay())
							<li class="mb-2 d-flex justify-content-between">
								<span class="text-muted">Duration:</span>
								<span class="fw-medium">{{ $event->getDurationDays() }} days</span>
							</li>
						@endif
						<li class="d-flex justify-content-between">
							<span class="text-muted">Holiday:</span>
							<span class="fw-medium">{{ $event->is_holiday ? 'Yes' : 'No' }}</span>
						</li>
					</ul>
				</div>
			</div>

			<!-- Back Button -->
			<a href="{{ route('portal.events') }}" class="btn btn-outline-secondary w-100">
				<i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Calendar
			</a>
		</div>
	</div>
</div>
@endsection
