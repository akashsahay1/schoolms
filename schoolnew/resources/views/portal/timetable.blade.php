@extends('layouts.portal')

@section('title', 'Timetable')
@section('page-title', 'My Timetable')

@section('breadcrumb')
	<li class="breadcrumb-item active">Timetable</li>
@endsection

@section('content')
<div class="container-fluid">
	<!-- Help Tip -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="help-tip">
				<i data-feather="info" class="me-2 text-primary" style="width: 18px; height: 18px;"></i>
				<strong>Your Weekly Schedule:</strong> Today's column ({{ now()->format('l') }}) is highlighted in blue. Break and lunch periods are shown in gray.
			</div>
		</div>
	</div>

	<!-- Class Info Card -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card bg-primary text-white">
				<div class="card-body py-3">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<h5 class="text-white mb-1">
								<i data-feather="book-open" class="me-2" style="width: 20px; height: 20px;"></i>
								Class {{ $student->schoolClass->name ?? 'N/A' }} - Section {{ $student->section->name ?? 'N/A' }}
							</h5>
							<small class="opacity-75">Academic Year: {{ $currentAcademicYear->name ?? 'Current' }}</small>
						</div>
						<button class="btn btn-light btn-sm" onclick="window.print()">
							<i data-feather="printer" style="width: 14px; height: 14px;"></i> Print
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Timetable -->
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-bordered mb-0">
							<thead>
								<tr class="bg-light">
									<th style="width: 100px;" class="text-center">Period</th>
									<th style="width: 100px;" class="text-center">Time</th>
									@foreach($days as $day)
										<th class="text-center {{ strtolower(now()->format('l')) === $day ? 'bg-primary text-white' : '' }}">
											{{ ucfirst($day) }}
											@if(strtolower(now()->format('l')) === $day)
												<br><small>(Today)</small>
											@endif
										</th>
									@endforeach
								</tr>
							</thead>
							<tbody>
								@foreach($periods as $period)
									@php
										$isBreak = in_array($period->type, ['break', 'lunch', 'recess']);
									@endphp
									<tr class="{{ $isBreak ? 'bg-light' : '' }}">
										<td class="text-center fw-medium align-middle">
											<span class="badge {{ $isBreak ? 'bg-secondary' : 'bg-primary' }}">{{ $period->name }}</span>
										</td>
										<td class="text-center align-middle">
											<small class="d-block">{{ \Carbon\Carbon::parse($period->start_time)->format('h:i A') }}</small>
											<small class="text-muted">to</small>
											<small class="d-block">{{ \Carbon\Carbon::parse($period->end_time)->format('h:i A') }}</small>
										</td>
										@foreach($days as $day)
											@php
												$isToday = strtolower(now()->format('l')) === $day;
											@endphp
											<td class="align-middle {{ $isToday ? 'bg-primary bg-opacity-10' : '' }}">
												@if($isBreak)
													<div class="text-center">
														<span class="badge bg-light text-dark">
															<i data-feather="{{ $period->type === 'lunch' ? 'coffee' : 'clock' }}" style="width: 12px; height: 12px;"></i>
															{{ ucfirst($period->type) }}
														</span>
													</div>
												@else
													@php
														$entry = $timetable->get($day)?->firstWhere('period_id', $period->id);
													@endphp
													@if($entry)
														<div class="text-center p-2">
															<strong class="d-block text-primary">{{ $entry->subject->name ?? '-' }}</strong>
															<small class="text-muted d-block">
																<i data-feather="user" style="width: 11px; height: 11px;"></i>
																{{ $entry->teacher->first_name ?? '' }} {{ $entry->teacher->last_name ?? '' }}
															</small>
															@if($entry->room)
																<span class="badge bg-light text-dark mt-1">
																	<i data-feather="map-pin" style="width: 10px; height: 10px;"></i>
																	{{ $entry->room }}
																</span>
															@endif
														</div>
													@else
														<div class="text-center text-muted">
															<i data-feather="minus" style="width: 14px; height: 14px;"></i>
														</div>
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
	</div>

	<!-- Legend -->
	<div class="row mt-3">
		<div class="col-12">
			<div class="card">
				<div class="card-body py-3">
					<div class="d-flex flex-wrap gap-4 align-items-center">
						<span class="d-flex align-items-center">
							<span class="badge bg-primary me-2">&nbsp;&nbsp;</span>
							Today's Schedule
						</span>
						<span class="d-flex align-items-center">
							<span class="badge bg-light text-dark me-2">
								<i data-feather="coffee" style="width: 12px; height: 12px;"></i>
							</span>
							Break/Lunch
						</span>
						<span class="d-flex align-items-center">
							<span class="badge bg-light text-dark me-2">
								<i data-feather="map-pin" style="width: 12px; height: 12px;"></i>
							</span>
							Room Number
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@push('styles')
<style>
	@media print {
		.sidebar-wrapper, .page-header, .breadcrumb, .btn, .help-tip, .legend-card { display: none !important; }
		.page-body { margin: 0 !important; padding: 0 !important; }
		.page-body-wrapper { margin-left: 0 !important; }
		.card { border: 1px solid #ddd !important; box-shadow: none !important; }
		.bg-primary { background-color: #7366ff !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
		.bg-primary.bg-opacity-10 { background-color: rgba(115, 102, 255, 0.1) !important; print-color-adjust: exact; }
	}
	.table td, .table th {
		vertical-align: middle;
	}
</style>
@endpush
@endsection
