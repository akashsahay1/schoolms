@extends('layouts.portal')

@section('title', 'Parent Dashboard')
@section('page-title', 'Parent Dashboard')

@section('breadcrumb')
	<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
	<!-- Welcome Banner -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card welcome-card parent-theme">
				<div class="card-body py-4">
					<div class="row align-items-center">
						<div class="col-md-8">
							<h4 class="text-white mb-2">
								<i data-feather="sun" style="width: 24px; height: 24px;"></i>
								Welcome back, {{ $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? 'Parent' }}!
							</h4>
							<p class="text-white mb-0 opacity-75">
								<i data-feather="calendar" style="width: 16px; height: 16px;"></i>
								{{ now()->format('l, F d, Y') }} | Academic Year: {{ $currentAcademicYear->name ?? 'Current' }}
							</p>
						</div>
						<div class="col-md-4 text-md-end mt-3 mt-md-0">
							<span class="badge bg-white text-primary py-2 px-3">
								<i data-feather="users" style="width: 14px; height: 14px;"></i>
								{{ $children->count() }} {{ Str::plural('Child', $children->count()) }}
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Help Tip -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="help-tip">
				<i data-feather="info" class="me-2 text-primary" style="width: 18px; height: 18px;"></i>
				<strong>Parent Dashboard:</strong> Monitor your children's academic progress, attendance, and fee status. Use the quick actions below to navigate to common tasks.
			</div>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="row mb-4">
		<div class="col-12">
			<h6 class="text-muted mb-3">
				<i data-feather="zap" style="width: 16px; height: 16px;"></i> Quick Actions
			</h6>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.attendance') }}" class="card quick-action-card h-100 text-center text-decoration-none">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-light-success mx-auto mb-2" style="width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
						<i data-feather="check-circle" class="text-success" style="width: 24px; height: 24px;"></i>
					</div>
					<h6 class="mb-0 small">Attendance</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.fees') }}" class="card quick-action-card h-100 text-center text-decoration-none">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-light-primary mx-auto mb-2" style="width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
						<i data-feather="credit-card" class="text-primary" style="width: 24px; height: 24px;"></i>
					</div>
					<h6 class="mb-0 small">Fees</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.timetable') }}" class="card quick-action-card h-100 text-center text-decoration-none">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-light-warning mx-auto mb-2" style="width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
						<i data-feather="clock" class="text-warning" style="width: 24px; height: 24px;"></i>
					</div>
					<h6 class="mb-0 small">Timetable</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.notices') }}" class="card quick-action-card h-100 text-center text-decoration-none">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-light-info mx-auto mb-2" style="width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
						<i data-feather="bell" class="text-info" style="width: 24px; height: 24px;"></i>
					</div>
					<h6 class="mb-0 small">Notices</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.events') }}" class="card quick-action-card h-100 text-center text-decoration-none">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-light-secondary mx-auto mb-2" style="width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
						<i data-feather="calendar" class="text-secondary" style="width: 24px; height: 24px;"></i>
					</div>
					<h6 class="mb-0 small">Events</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.contact') }}" class="card quick-action-card h-100 text-center text-decoration-none">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-light-danger mx-auto mb-2" style="width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
						<i data-feather="message-circle" class="text-danger" style="width: 24px; height: 24px;"></i>
					</div>
					<h6 class="mb-0 small">Contact</h6>
				</div>
			</a>
		</div>
	</div>

	<!-- Children Overview -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card">
				<div class="card-header pb-0">
					<h5 class="mb-0">
						<i data-feather="users" style="width: 18px; height: 18px;"></i> My Children
					</h5>
				</div>
				<div class="card-body">
					@if($children->count() > 0)
						<div class="row">
							@foreach($children as $child)
								<div class="col-xl-6 mb-4">
									<div class="card border info-card h-100">
										<div class="card-body">
											<div class="d-flex align-items-start mb-3">
												<img src="{{ $child->photo_url }}" alt="{{ $child->full_name }}" class="rounded-circle me-3 shadow-sm" width="70" height="70" style="object-fit: cover; border: 3px solid #fff;">
												<div class="flex-grow-1">
													<h5 class="mb-1">{{ $child->full_name }}</h5>
													<p class="text-muted mb-2">
														<i data-feather="book-open" style="width: 14px; height: 14px;"></i>
														{{ $child->schoolClass->name ?? 'N/A' }} - {{ $child->section->name ?? 'N/A' }}
													</p>
													<div class="d-flex flex-wrap gap-1">
														<span class="badge badge-light-primary">
															<i data-feather="hash" style="width: 12px; height: 12px;"></i>
															Adm: {{ $child->admission_no }}
														</span>
														@if($child->roll_no)
															<span class="badge badge-light-secondary">
																<i data-feather="list" style="width: 12px; height: 12px;"></i>
																Roll: {{ $child->roll_no }}
															</span>
														@endif
													</div>
												</div>
											</div>

											@if(isset($childrenStats[$child->id]))
												<hr class="my-3">
												<div class="row text-center g-2">
													<div class="col-4">
														<div class="bg-light-success rounded p-2">
															<h5 class="text-success mb-0">{{ $childrenStats[$child->id]['attendance']['percentage'] }}%</h5>
															<small class="text-muted d-block">Attendance</small>
														</div>
													</div>
													<div class="col-4">
														<div class="bg-light-primary rounded p-2">
															<h6 class="text-primary mb-0">Rs. {{ number_format($childrenStats[$child->id]['fees']['total_paid'], 0) }}</h6>
															<small class="text-muted d-block">Paid</small>
														</div>
													</div>
													<div class="col-4">
														<div class="bg-light-{{ $childrenStats[$child->id]['fees']['total_due'] > 0 ? 'danger' : 'success' }} rounded p-2">
															<h6 class="text-{{ $childrenStats[$child->id]['fees']['total_due'] > 0 ? 'danger' : 'success' }} mb-0">Rs. {{ number_format($childrenStats[$child->id]['fees']['total_due'], 0) }}</h6>
															<small class="text-muted d-block">Due</small>
														</div>
													</div>
												</div>
											@endif
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<div class="text-center py-5">
							<i data-feather="users" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
							<h6 class="text-muted">No Children Linked</h6>
							<p class="text-muted mb-0">No children are currently linked to your account. Please contact the school office.</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<!-- Recent Notices -->
		<div class="col-xl-6 mb-4">
			<div class="card h-100">
				<div class="card-header pb-0">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">
							<i data-feather="bell" style="width: 18px; height: 18px;"></i> Recent Notices
						</h5>
						<a href="{{ route('portal.notices') }}" class="btn btn-sm btn-outline-primary">
							View All <i data-feather="arrow-right" style="width: 14px; height: 14px;"></i>
						</a>
					</div>
				</div>
				<div class="card-body">
					@if($notices->count() > 0)
						<ul class="list-group list-group-flush">
							@foreach($notices as $notice)
								<li class="list-group-item px-0 py-3">
									<div class="d-flex justify-content-between align-items-start">
										<div class="flex-grow-1">
											<div class="mb-1">
												<span class="badge {{ $notice->getTypeBadgeClass() }}">
													{{ $notice->getTypeLabel() }}
												</span>
											</div>
											<h6 class="mb-1">{{ Str::limit($notice->title, 50) }}</h6>
											<small class="text-muted">
												<i data-feather="calendar" style="width: 12px; height: 12px;"></i>
												{{ $notice->publish_date->format('M d, Y') }}
											</small>
										</div>
										<a href="{{ route('portal.notices.show', $notice) }}" class="btn btn-sm btn-light">
											<i data-feather="eye" style="width: 14px; height: 14px;"></i>
										</a>
									</div>
								</li>
							@endforeach
						</ul>
					@else
						<div class="text-center py-4">
							<i data-feather="bell-off" class="text-muted mb-2" style="width: 32px; height: 32px;"></i>
							<p class="text-muted mb-0 small">No notices available</p>
						</div>
					@endif
				</div>
			</div>
		</div>

		<!-- Upcoming Events -->
		<div class="col-xl-6 mb-4">
			<div class="card h-100">
				<div class="card-header pb-0">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">
							<i data-feather="calendar" style="width: 18px; height: 18px;"></i> Upcoming Events
						</h5>
						<a href="{{ route('portal.events') }}" class="btn btn-sm btn-outline-primary">
							View Calendar <i data-feather="arrow-right" style="width: 14px; height: 14px;"></i>
						</a>
					</div>
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
												<a href="{{ route('portal.events.show', $event) }}" class="text-dark text-decoration-none">{{ Str::limit($event->title, 40) }}</a>
											</h6>
											<div class="d-flex flex-wrap gap-1 align-items-center">
												<span class="badge py-1 px-2" style="background-color: {{ $event->color }}; color: white; font-size: 10px;">
													{{ $event->getTypeLabel() }}
												</span>
												<small class="text-muted">
													<i data-feather="clock" style="width: 12px; height: 12px;"></i>
													{{ $event->start_date->diffForHumans() }}
												</small>
											</div>
										</div>
									</div>
								</li>
							@endforeach
						</ul>
					@else
						<div class="text-center py-4">
							<i data-feather="calendar" class="text-muted mb-2" style="width: 32px; height: 32px;"></i>
							<p class="text-muted mb-0 small">No upcoming events</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
