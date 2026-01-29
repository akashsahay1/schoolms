@extends('layouts.teacher-portal')

@section('title', 'My Timetable')
@section('page-title', 'My Timetable')

@section('breadcrumb')
<li class="breadcrumb-item active">My Timetable</li>
@endsection

@section('content')
<div class="row">
	<!-- Help Tip -->
	<div class="col-12 mb-4">
		<div class="help-tip">
			<i data-feather="info" class="me-2 text-primary"></i>
			<strong>Your Weekly Schedule:</strong> This shows all your assigned classes for the week. Today's classes are highlighted.
		</div>
	</div>

	<!-- Timetable Card -->
	<div class="col-12">
		<div class="card">
			<div class="card-header pb-0">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Weekly Timetable</h5>
					<span class="badge bg-primary">{{ now()->format('l, M d, Y') }}</span>
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead class="bg-light">
							<tr>
								<th style="width: 150px;">Period / Time</th>
								@foreach($days as $day)
									<th class="text-center {{ strtolower(now()->format('l')) == $day ? 'bg-primary text-white' : '' }}">
										{{ ucfirst($day) }}
									</th>
								@endforeach
							</tr>
						</thead>
						<tbody>
							@foreach($periods as $period)
								<tr>
									<td class="bg-light">
										<strong>{{ $period->name }}</strong>
										<br>
										<small class="text-muted">
											{{ \Carbon\Carbon::parse($period->start_time)->format('h:i A') }}
											-
											{{ \Carbon\Carbon::parse($period->end_time)->format('h:i A') }}
										</small>
									</td>
									@foreach($days as $day)
										@php
											$slot = isset($timetable[$day]) ? $timetable[$day]->firstWhere('period_id', $period->id) : null;
											$isToday = strtolower(now()->format('l')) == $day;
										@endphp
										<td class="text-center {{ $isToday ? 'bg-primary bg-opacity-10' : '' }}">
											@if($slot)
												<div class="p-2">
													<strong class="d-block text-primary">{{ $slot->subject->name ?? 'N/A' }}</strong>
													<small class="d-block text-muted">
														{{ $slot->schoolClass->name ?? '' }}
														@if($slot->section)
															- {{ $slot->section->name }}
														@endif
													</small>
													@if($slot->room)
														<span class="badge bg-secondary mt-1">{{ $slot->room }}</span>
													@endif
												</div>
											@else
												<span class="text-muted">-</span>
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

	<!-- Legend -->
	<div class="col-12 mt-3">
		<div class="card">
			<div class="card-body py-3">
				<div class="d-flex flex-wrap gap-4 align-items-center">
					<span class="d-flex align-items-center">
						<span class="badge bg-primary me-2">&nbsp;&nbsp;</span>
						Today's Column
					</span>
					<span class="d-flex align-items-center">
						<span class="badge bg-secondary me-2">Room</span>
						Room Number
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
