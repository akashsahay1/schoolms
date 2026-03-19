@extends('layouts.portal')

@section('title', 'Parent Dashboard')
@section('page-title', 'Parent Dashboard')

@section('breadcrumb')
	<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<style>
	.welcome-card .card-body,
	.welcome-card .card-body *:not(.bg-white):not(.bg-white *):not(.bg-light):not(.bg-light *),
	.welcome-card .card-body p,
	.welcome-card .card-body h1,
	.welcome-card .card-body h2,
	.welcome-card .card-body h3,
	.welcome-card .card-body h4,
	.welcome-card .card-body h5,
	.welcome-card .card-body h6,
	.welcome-card .card-body strong,
	.welcome-card .card-body i {
		color: #ffffff !important;
	}
	.welcome-card .card-body .bg-white,
	.welcome-card .card-body .bg-white *,
	.welcome-card .card-body .bg-light,
	.welcome-card .card-body .bg-light * {
		color: #000 !important;
	}
	.welcome-card .card-body svg,
	.welcome-card .card-body [data-feather] {
		stroke: #ffffff !important;
		color: #ffffff !important;
	}
	.quick-action-card {
		border: 1px solid #f0f0f0 !important;
		border-radius: 12px !important;
	}
	.quick-action-card .quick-action-icon {
		width: 48px;
		height: 48px;
		border-radius: 10px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.child-card {
		border-radius: 12px;
		border: 1px solid #eee;
		transition: transform 0.2s ease, box-shadow 0.2s ease;
	}
	.child-card:hover {
		transform: translateY(-3px);
		box-shadow: 0 6px 20px rgba(0,0,0,0.1);
	}
	.child-stat-box {
		border-radius: 10px;
		padding: 10px 8px;
		text-align: center;
	}
	.child-stat-box h5,
	.child-stat-box h6 {
		margin-bottom: 0;
		font-weight: 600;
	}
	.child-stat-box small {
		font-size: 11px;
	}
	.notice-item {
		border-radius: 8px;
		transition: background 0.2s;
	}
	.notice-item:hover {
		background: #f8f9fa;
	}
	.event-date-box {
		min-width: 55px;
		text-align: center;
		border-radius: 10px;
		padding: 8px 10px;
	}
</style>

<div class="container-fluid">
	<!-- Welcome Banner -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card welcome-card parent-theme mb-0">
				<div class="card-body py-4 px-4">
					<div class="row align-items-center">
						<div class="col-md-8">
							<p class="mb-1" style="opacity: 0.8; font-size: 14px;">
								<i data-feather="sun" style="width: 16px; height: 16px;"></i>
								Good {{ now()->format('H') < 12 ? 'Morning' : (now()->format('H') < 17 ? 'Afternoon' : 'Evening') }}
							</p>
							<h4 class="mb-2 fw-bold">
								Welcome back, {{ $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? 'Parent' }}!
							</h4>
							<p class="mb-0" style="opacity: 0.85;">
								<i data-feather="calendar" style="width: 16px; height: 16px;"></i>
								{{ now()->format('l, F d, Y') }} | Academic Year: {{ $currentAcademicYear->name ?? 'Current' }}
							</p>
						</div>
						<div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex align-items-center justify-content-md-end justify-content-start">
							<span class="badge bg-white py-2 px-3 d-inline-flex align-items-center gap-1" style="font-size: 13px; color: #7366ff !important;">
								<i data-feather="users" style="width: 14px; height: 14px; stroke: #7366ff !important;"></i>
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
			<div class="help-tip d-flex align-items-start gap-2">
				<i data-feather="info" class="text-primary flex-shrink-0 mt-1" style="width: 18px; height: 18px;"></i>
				<div>
					<strong>Parent Dashboard:</strong> Monitor your children's academic progress, attendance, and fee status. Use the quick actions below to navigate to common tasks.
				</div>
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
			<a href="{{ route('portal.attendance') }}" class="card quick-action-card h-100 text-center text-decoration-none mb-0">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-success bg-opacity-10 mx-auto mb-2">
						<i data-feather="check-circle" class="text-success" style="width: 22px; height: 22px;"></i>
					</div>
					<h6 class="mb-0 small">Attendance</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.fees.overview') }}" class="card quick-action-card h-100 text-center text-decoration-none mb-0">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-primary bg-opacity-10 mx-auto mb-2">
						<i data-feather="credit-card" class="text-primary" style="width: 22px; height: 22px;"></i>
					</div>
					<h6 class="mb-0 small">Fees</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.timetable') }}" class="card quick-action-card h-100 text-center text-decoration-none mb-0">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-warning bg-opacity-10 mx-auto mb-2">
						<i data-feather="clock" class="text-warning" style="width: 22px; height: 22px;"></i>
					</div>
					<h6 class="mb-0 small">Timetable</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.notices') }}" class="card quick-action-card h-100 text-center text-decoration-none mb-0">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-info bg-opacity-10 mx-auto mb-2">
						<i data-feather="bell" class="text-info" style="width: 22px; height: 22px;"></i>
					</div>
					<h6 class="mb-0 small">Notices</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.events') }}" class="card quick-action-card h-100 text-center text-decoration-none mb-0">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-secondary bg-opacity-10 mx-auto mb-2">
						<i data-feather="calendar" class="text-secondary" style="width: 22px; height: 22px;"></i>
					</div>
					<h6 class="mb-0 small">Events</h6>
				</div>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.contact') }}" class="card quick-action-card h-100 text-center text-decoration-none mb-0">
				<div class="card-body py-3">
					<div class="quick-action-icon bg-danger bg-opacity-10 mx-auto mb-2">
						<i data-feather="message-circle" class="text-danger" style="width: 22px; height: 22px;"></i>
					</div>
					<h6 class="mb-0 small">Contact</h6>
				</div>
			</a>
		</div>
	</div>

	<!-- Children Overview -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card mb-0">
				<div class="card-header pb-0 border-0">
					<h5 class="mb-0">
						<i data-feather="users" style="width: 18px; height: 18px;" class="me-2"></i>My Children
					</h5>
				</div>
				<div class="card-body pt-3">
					@if($children->count() > 0)
						<div class="row">
							@foreach($children as $child)
								<div class="col-xl-6 mb-4">
									<div class="card child-card h-100 mb-0">
										<div class="card-body">
											<div class="d-flex align-items-start mb-3">
												<img src="{{ $child->photo_url }}" alt="{{ $child->full_name }}" class="rounded-circle me-3 shadow-sm" width="65" height="65" style="object-fit: cover; border: 3px solid #fff;">
												<div class="flex-grow-1">
													<h5 class="mb-1">{{ $child->full_name }}</h5>
													<p class="text-muted mb-2" style="font-size: 14px;">
														<i data-feather="book-open" style="width: 14px; height: 14px;"></i>
														{{ $child->schoolClass->name ?? 'N/A' }} - {{ $child->section->name ?? 'N/A' }}
													</p>
													<div class="d-flex flex-wrap gap-1">
														<span class="badge bg-primary bg-opacity-10 text-primary">
															<i data-feather="hash" style="width: 12px; height: 12px;"></i>
															Adm: {{ $child->admission_no }}
														</span>
														@if($child->roll_no)
															<span class="badge bg-secondary bg-opacity-10 text-secondary">
																<i data-feather="list" style="width: 12px; height: 12px;"></i>
																Roll: {{ $child->roll_no }}
															</span>
														@endif
													</div>
												</div>
											</div>

											@if(isset($childrenStats[$child->id]))
												<hr class="my-3">
												<div class="row g-2">
													<div class="col-4">
														<div class="child-stat-box bg-success bg-opacity-10">
															<h5 class="text-success">{{ $childrenStats[$child->id]['attendance']['percentage'] }}%</h5>
															<small class="text-muted d-block">Attendance</small>
														</div>
													</div>
													<div class="col-4">
														<div class="child-stat-box bg-primary bg-opacity-10">
															<h6 class="text-primary">₹{{ number_format($childrenStats[$child->id]['fees']['total_paid'], 0) }}</h6>
															<small class="text-muted d-block">Paid</small>
														</div>
													</div>
													<div class="col-4">
														<div class="child-stat-box bg-{{ $childrenStats[$child->id]['fees']['total_due'] > 0 ? 'danger' : 'success' }} bg-opacity-10">
															<h6 class="text-{{ $childrenStats[$child->id]['fees']['total_due'] > 0 ? 'danger' : 'success' }}">₹{{ number_format($childrenStats[$child->id]['fees']['total_due'], 0) }}</h6>
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
			<div class="card h-100 mb-0">
				<div class="card-header pb-0 border-0">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">
							<i data-feather="bell" style="width: 18px; height: 18px;" class="me-2"></i>Recent Notices
						</h5>
						<a href="{{ route('portal.notices') }}" class="btn btn-sm btn-outline-primary">
							View All
						</a>
					</div>
				</div>
				<div class="card-body pt-3">
					@if($notices->count() > 0)
						<ul class="list-group list-group-flush">
							@foreach($notices as $notice)
								<li class="list-group-item px-0 py-3 border-0 notice-item">
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
										<a href="{{ route('portal.notices.show', $notice) }}" class="btn btn-sm btn-light ms-2">
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
			<div class="card h-100 mb-0">
				<div class="card-header pb-0 border-0">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">
							<i data-feather="calendar" style="width: 18px; height: 18px;" class="me-2"></i>Upcoming Events
						</h5>
						<a href="{{ route('portal.events') }}" class="btn btn-sm btn-outline-primary">
							View Calendar
						</a>
					</div>
				</div>
				<div class="card-body pt-3">
					@if($upcomingEvents->count() > 0)
						<ul class="list-group list-group-flush">
							@foreach($upcomingEvents as $event)
								<li class="list-group-item px-0 py-3 border-0 notice-item">
									<div class="d-flex align-items-start">
										<div class="me-3 event-date-box bg-light">
											<div class="fw-bold text-primary" style="font-size: 18px;">{{ $event->start_date->format('d') }}</div>
											<div class="small text-muted">{{ $event->start_date->format('M') }}</div>
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
