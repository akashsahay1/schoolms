@extends('layouts.portal')

@section('title', 'Events')
@section('page-title', 'Events Calendar')

@section('breadcrumb')
	<li class="breadcrumb-item active">Events</li>
@endsection

@section('content')
<div class="container-fluid">
	<!-- Help Tip -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="help-tip">
				<i data-feather="info" class="me-2 text-primary" style="width: 18px; height: 18px;"></i>
				<strong>School Events:</strong> View upcoming school events, activities, and holidays. Click on an event to see more details. Today is highlighted on the calendar.
			</div>
		</div>
	</div>

	<!-- Month Navigation -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card bg-primary text-white">
				<div class="card-body py-3">
					<div class="d-flex justify-content-between align-items-center">
						@php
							$prevMonth = $month == 1 ? 12 : $month - 1;
							$prevYear = $month == 1 ? $year - 1 : $year;
							$nextMonth = $month == 12 ? 1 : $month + 1;
							$nextYear = $month == 12 ? $year + 1 : $year;
						@endphp
						<a href="{{ route('portal.events', ['month' => $prevMonth, 'year' => $prevYear]) }}" class="btn btn-light btn-sm">
							<i data-feather="chevron-left" style="width: 16px; height: 16px;"></i> Previous
						</a>
						<h4 class="mb-0 text-white">
							<i data-feather="calendar" style="width: 24px; height: 24px;"></i>
							{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}
						</h4>
						<a href="{{ route('portal.events', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="btn btn-light btn-sm">
							Next <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<!-- Calendar -->
		<div class="col-xl-8 mb-4">
			<div class="card">
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-bordered mb-0 text-center">
							<thead class="bg-primary text-white">
								<tr>
									<th class="py-3">Sun</th>
									<th class="py-3">Mon</th>
									<th class="py-3">Tue</th>
									<th class="py-3">Wed</th>
									<th class="py-3">Thu</th>
									<th class="py-3">Fri</th>
									<th class="py-3">Sat</th>
								</tr>
							</thead>
							<tbody>
								@foreach($calendarData as $week)
									<tr>
										@foreach($week as $day)
											<td class="{{ !$day['inMonth'] ? 'text-muted bg-light' : '' }} {{ $day['isToday'] ? 'bg-primary bg-opacity-10 border-primary border-2' : '' }}" style="height: 100px; vertical-align: top; padding: 8px;">
												@if($day['inMonth'])
													<div class="fw-bold mb-1 {{ $day['isToday'] ? 'text-primary' : '' }}">
														{{ $day['day'] }}
														@if($day['isToday'])
															<span class="badge bg-primary rounded-pill ms-1" style="font-size: 9px;">Today</span>
														@endif
													</div>
													@foreach($day['events']->take(2) as $event)
														<a href="{{ route('portal.events.show', $event) }}" class="badge d-block mb-1 text-truncate text-start" style="background-color: {{ $event->color }}; color: white; font-size: 10px; padding: 4px 6px;" title="{{ $event->title }}">
															{{ Str::limit($event->title, 15) }}
														</a>
													@endforeach
													@if($day['events']->count() > 2)
														<small class="text-muted d-block">+{{ $day['events']->count() - 2 }} more</small>
													@endif
												@endif
											</td>
										@endforeach
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Upcoming Events -->
		<div class="col-xl-4 mb-4">
			<div class="card h-100">
				<div class="card-header pb-0">
					<h5 class="mb-0">
						<i data-feather="calendar" style="width: 18px; height: 18px;"></i> Upcoming Events
					</h5>
				</div>
				<div class="card-body">
					@if($upcomingEvents->count() > 0)
						<ul class="list-group list-group-flush">
							@foreach($upcomingEvents as $event)
								<li class="list-group-item px-0 py-3">
									<div class="d-flex align-items-start">
										<div class="me-3 text-center" style="min-width: 55px;">
											<div class="bg-light rounded p-2">
												<div class="fw-bold text-primary fs-5">{{ $event->start_date->format('d') }}</div>
												<div class="small text-muted">{{ $event->start_date->format('M') }}</div>
											</div>
										</div>
										<div class="flex-grow-1">
											<h6 class="mb-1">
												<a href="{{ route('portal.events.show', $event) }}" class="text-dark text-decoration-none">{{ $event->title }}</a>
											</h6>
											<div class="d-flex flex-wrap gap-1 align-items-center">
												<span class="badge py-1 px-2" style="background-color: {{ $event->color }}; color: white; font-size: 10px;">
													{{ $event->getTypeLabel() }}
												</span>
												@if($event->venue)
													<span class="text-muted small">
														<i data-feather="map-pin" style="width: 12px; height: 12px;"></i> {{ Str::limit($event->venue, 20) }}
													</span>
												@endif
											</div>
											<small class="text-muted d-block mt-1">
												<i data-feather="clock" style="width: 12px; height: 12px;"></i>
												{{ $event->start_date->diffForHumans() }}
											</small>
										</div>
									</div>
								</li>
							@endforeach
						</ul>
					@else
						<div class="text-center py-5">
							<i data-feather="calendar" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
							<p class="text-muted mb-0">No upcoming events</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	<!-- Legend -->
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body py-3">
					<div class="d-flex flex-wrap gap-4 align-items-center">
						<strong class="me-2">
							<i data-feather="info" style="width: 14px; height: 14px;"></i> Tip:
						</strong>
						<span class="text-muted">Click on any event to view full details including time, venue, and description.</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
