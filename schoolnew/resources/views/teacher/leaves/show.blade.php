@extends('layouts.teacher-portal')

@section('title', 'Leave Application Details')
@section('page-title', 'Leave Application Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.leaves.index') }}">My Leave</a></li>
<li class="breadcrumb-item active">Application Details</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-start mb-4">
					<div>
						<h5 class="mb-1">{{ $leave->getLeaveTypeLabel() }}</h5>
						<small class="text-muted">Applied on {{ $leave->created_at->format('M d, Y') }}</small>
					</div>
					<div>
						@switch($leave->status)
							@case('pending')
								<span class="badge bg-warning fs-6">Pending</span>
								@break
							@case('approved')
								<span class="badge bg-success fs-6">Approved</span>
								@break
							@case('rejected')
								<span class="badge bg-danger fs-6">Rejected</span>
								@break
							@case('cancelled')
								<span class="badge bg-secondary fs-6">Cancelled</span>
								@break
						@endswitch
					</div>
				</div>

				<div class="row g-4">
					<div class="col-md-6">
						<label class="text-muted small">From Date</label>
						<p class="mb-0 fw-medium">{{ $leave->from_date->format('M d, Y (l)') }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">To Date</label>
						<p class="mb-0 fw-medium">{{ $leave->to_date->format('M d, Y (l)') }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Total Days</label>
						<p class="mb-0 fw-medium">{{ $leave->from_date->diffInDays($leave->to_date) + 1 }} days</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Leave Type</label>
						<p class="mb-0 fw-medium">{{ $leave->getLeaveTypeLabel() }}</p>
					</div>
					<div class="col-12">
						<label class="text-muted small">Reason</label>
						<p class="mb-0">{{ $leave->reason }}</p>
					</div>

					@if($leave->attachment)
						<div class="col-12">
							<label class="text-muted small">Attachment</label>
							<p class="mb-0">
								<a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
									<i data-feather="download" style="width: 14px; height: 14px;"></i> Download Attachment
								</a>
							</p>
						</div>
					@endif

					@if($leave->status == 'rejected' && $leave->admin_remarks)
						<div class="col-12">
							<div class="alert alert-danger mb-0">
								<strong>Rejection Reason:</strong> {{ $leave->admin_remarks }}
							</div>
						</div>
					@endif

					@if($leave->status == 'approved' && $leave->approved_by)
						<div class="col-12">
							<div class="alert alert-success mb-0">
								<strong>Approved by:</strong> {{ $leave->approvedByUser->name ?? 'Admin' }}
								@if($leave->approved_at)
									on {{ $leave->approved_at->format('M d, Y') }}
								@endif
							</div>
						</div>
					@endif
				</div>
			</div>
			<div class="card-footer">
				<a href="{{ route('teacher.leaves.index') }}" class="btn btn-secondary">
					<i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Applications
				</a>
				@if($leave->status == 'pending')
					<form action="{{ route('teacher.leaves.cancel', $leave) }}" method="POST" class="d-inline delete-form">
						@csrf
						<button type="button" class="btn btn-danger delete-confirm" data-name="this leave application">
							<i data-feather="x" style="width: 14px; height: 14px;"></i> Cancel Application
						</button>
					</form>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
