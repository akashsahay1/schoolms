@extends('layouts.app')

@section('title', 'Events')
@section('page-title', 'Events')

@section('breadcrumb')
	<li class="breadcrumb-item">Communication</li>
	<li class="breadcrumb-item active">Events</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Events</h5>
					<a href="{{ route('admin.events.create') }}" class="btn btn-primary">
						<i class="fa fa-plus me-1"></i> Add Event
					</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.events.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<select name="type" class="form-select">
								<option value="">All Types</option>
								@foreach(\App\Models\Event::TYPES as $key => $label)
									<option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<select name="month" class="form-select">
								<option value="">All Months</option>
								@for($m = 1; $m <= 12; $m++)
									<option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
								@endfor
							</select>
						</div>
						<div class="col-md-2">
							<select name="year" class="form-select">
								@for($y = date('Y'); $y >= date('Y') - 2; $y--)
									<option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
								@endfor
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Filter
							</button>
						</div>
						@if(request()->hasAny(['type', 'month', 'year']))
							<div class="col-md-1">
								<a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary w-100" title="Clear Filters">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Events Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Event</th>
								<th>Type</th>
								<th>Date</th>
								<th>Venue</th>
								<th>Holiday</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($events as $event)
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<div class="me-2" style="width: 4px; height: 40px; background-color: {{ $event->color }}; border-radius: 2px;"></div>
											<div>
												<strong>{{ Str::limit($event->title, 35) }}</strong>
												@if($event->is_public)
													<span class="badge badge-light-info ms-1">Public</span>
												@endif
											</div>
										</div>
									</td>
									<td><span class="badge {{ $event->getTypeBadgeClass() }}">{{ $event->getTypeLabel() }}</span></td>
									<td>
										{{ $event->start_date->format('M d, Y') }}
										@if($event->isMultiDay())
											<br><small class="text-muted">to {{ $event->end_date->format('M d, Y') }}</small>
										@endif
									</td>
									<td>{{ $event->venue ?? '-' }}</td>
									<td>
										@if($event->is_holiday)
											<span class="badge badge-light-danger">Yes</span>
										@else
											<span class="text-muted">No</span>
										@endif
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.events.show', $event) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.events.edit', $event) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $event->title }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="calendar" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No events found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
				@if($events->hasPages())
					<div class="mt-3">
						{{ $events->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
