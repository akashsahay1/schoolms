@extends('layouts.teacher-portal')

@section('title', 'Leave Balance')
@section('page-title', 'Leave Balance')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.leaves.index') }}">My Leave</a></li>
<li class="breadcrumb-item active">Leave Balance</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 mb-4">
		<div class="help-tip">
			<i data-feather="info" class="me-2 text-primary"></i>
			<strong>Leave Balance:</strong> This shows your allocated leave for the current year and how much you've used.
		</div>
	</div>

	@foreach($leaveTypes as $type)
		@php
			$balance = $balances->firstWhere('leave_type_id', $type->id);
			$allocated = $balance ? $balance->total_available : $type->allowed_days;
			$used = $balance ? $balance->used_days : $usedLeaves->get($type->code, 0);
			$remaining = $allocated - $used;
			$percentage = $allocated > 0 ? ($used / $allocated) * 100 : 0;
		@endphp
		<div class="col-xl-4 col-md-6 mb-4">
			<div class="card h-100">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h6 class="mb-0">{{ $type->name }}</h6>
						@if($type->is_paid)
							<span class="badge bg-success">Paid</span>
						@else
							<span class="badge bg-secondary">Unpaid</span>
						@endif
					</div>

					<div class="row text-center mb-3">
						<div class="col-4">
							<h4 class="mb-0 text-primary">{{ $allocated }}</h4>
							<small class="text-muted">Allocated</small>
						</div>
						<div class="col-4">
							<h4 class="mb-0 text-danger">{{ $used }}</h4>
							<small class="text-muted">Used</small>
						</div>
						<div class="col-4">
							<h4 class="mb-0 text-success">{{ max(0, $remaining) }}</h4>
							<small class="text-muted">Remaining</small>
						</div>
					</div>

					<div class="progress" style="height: 8px;">
						<div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 50 ? 'bg-warning' : 'bg-success') }}" role="progressbar" style="width: {{ min(100, $percentage) }}%"></div>
					</div>
					<small class="text-muted mt-2 d-block">{{ number_format($percentage, 0) }}% used</small>
				</div>
			</div>
		</div>
	@endforeach

	@if($leaveTypes->count() == 0)
		<div class="col-12">
			<div class="card">
				<div class="card-body text-center py-5">
					<i data-feather="alert-circle" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
					<h5 class="text-muted">No Leave Types Configured</h5>
					<p class="text-muted mb-0">Please contact the administrator to set up leave types.</p>
				</div>
			</div>
		</div>
	@endif
</div>

<div class="row mt-4">
	<div class="col-12">
		<a href="{{ route('teacher.leaves.create') }}" class="btn btn-primary">
			<i data-feather="plus" style="width: 14px; height: 14px;"></i> Apply for Leave
		</a>
		<a href="{{ route('teacher.leaves.index') }}" class="btn btn-secondary">
			<i data-feather="list" style="width: 14px; height: 14px;"></i> View Applications
		</a>
	</div>
</div>
@endsection
