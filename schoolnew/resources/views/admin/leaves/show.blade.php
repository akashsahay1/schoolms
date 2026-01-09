@extends('layouts.app')

@section('title', 'Leave Application Details')
@section('page-title', 'Leave Application Details')

@section('breadcrumb')
	<li class="breadcrumb-item">Attendance</li>
	<li class="breadcrumb-item"><a href="{{ route('admin.leaves.index') }}">Leave Applications</a></li>
	<li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="row">
	<div class="col-xl-4">
		<!-- Student Info Card -->
		<div class="card">
			<div class="card-header">
				<h5>Student Information</h5>
			</div>
			<div class="card-body">
				<div class="text-center mb-4">
					@if($leave->student && $leave->student->photo)
						<img src="{{ asset('storage/' . $leave->student->photo) }}" alt="" class="rounded-circle" width="100" height="100">
					@else
						<div class="bg-light-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
							<span class="text-primary fs-1">{{ $leave->student ? substr($leave->student->first_name, 0, 1) : '?' }}</span>
						</div>
					@endif
					<h5 class="mt-3 mb-1">{{ $leave->student->first_name ?? 'N/A' }} {{ $leave->student->last_name ?? '' }}</h5>
					<p class="text-muted mb-0">{{ $leave->student->admission_no ?? 'N/A' }}</p>
				</div>

				<ul class="list-group list-group-flush">
					<li class="list-group-item d-flex justify-content-between align-items-center px-0">
						<span class="text-muted">Class</span>
						<span class="fw-medium">{{ $leave->student->schoolClass->name ?? 'N/A' }} - {{ $leave->student->section->name ?? '' }}</span>
					</li>
					<li class="list-group-item d-flex justify-content-between align-items-center px-0">
						<span class="text-muted">Roll Number</span>
						<span class="fw-medium">{{ $leave->student->roll_no ?? 'N/A' }}</span>
					</li>
					<li class="list-group-item d-flex justify-content-between align-items-center px-0">
						<span class="text-muted">Parent Phone</span>
						<span class="fw-medium">{{ $leave->student->parent_phone ?? $leave->student->father_phone ?? 'N/A' }}</span>
					</li>
					<li class="list-group-item d-flex justify-content-between align-items-center px-0">
						<span class="text-muted">Email</span>
						<span class="fw-medium">{{ $leave->student->user->email ?? 'N/A' }}</span>
					</li>
				</ul>
			</div>
		</div>

		<!-- Status Card -->
		<div class="card">
			<div class="card-header">
				<h5>Application Status</h5>
			</div>
			<div class="card-body text-center">
				@if($leave->status === 'pending')
					<div class="bg-light-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
						<i data-feather="clock" class="text-warning" style="width: 40px; height: 40px;"></i>
					</div>
					<h4 class="text-warning">Pending Approval</h4>
					<p class="text-muted">Waiting for admin action</p>
				@elseif($leave->status === 'approved')
					<div class="bg-light-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
						<i data-feather="check-circle" class="text-success" style="width: 40px; height: 40px;"></i>
					</div>
					<h4 class="text-success">Approved</h4>
					<p class="text-muted mb-1">By {{ $leave->approvedByUser->name ?? 'Admin' }}</p>
					<small class="text-muted">{{ $leave->approved_at ? $leave->approved_at->format('M d, Y h:i A') : '' }}</small>
				@elseif($leave->status === 'rejected')
					<div class="bg-light-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
						<i data-feather="x-circle" class="text-danger" style="width: 40px; height: 40px;"></i>
					</div>
					<h4 class="text-danger">Rejected</h4>
					<p class="text-muted mb-1">By {{ $leave->approvedByUser->name ?? 'Admin' }}</p>
					<small class="text-muted">{{ $leave->approved_at ? $leave->approved_at->format('M d, Y h:i A') : '' }}</small>
				@else
					<div class="bg-light-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
						<i data-feather="slash" class="text-secondary" style="width: 40px; height: 40px;"></i>
					</div>
					<h4 class="text-secondary">Cancelled</h4>
					<p class="text-muted">Cancelled by student</p>
				@endif
			</div>
		</div>
	</div>

	<div class="col-xl-8">
		<!-- Leave Details Card -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Leave Application Details</h5>
					<span class="badge {{ $leave->getStatusBadgeClass() }} fs-6">{{ $leave->getStatusLabel() }}</span>
				</div>
			</div>
			<div class="card-body">
				<div class="row mb-4">
					<div class="col-md-6">
						<div class="mb-3">
							<label class="text-muted small">Leave Type</label>
							<p class="mb-0 fw-medium"><span class="badge badge-light-info">{{ $leave->getLeaveTypeLabel() }}</span></p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="text-muted small">Total Days</label>
							<p class="mb-0 fw-medium">{{ $leave->total_days }} Day(s)</p>
						</div>
					</div>
				</div>

				<div class="row mb-4">
					<div class="col-md-6">
						<div class="mb-3">
							<label class="text-muted small">From Date</label>
							<p class="mb-0 fw-medium">{{ $leave->from_date->format('l, M d, Y') }}</p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="text-muted small">To Date</label>
							<p class="mb-0 fw-medium">{{ $leave->to_date->format('l, M d, Y') }}</p>
						</div>
					</div>
				</div>

				<div class="mb-4">
					<label class="text-muted small">Reason for Leave</label>
					<div class="p-3 bg-light rounded">
						<p class="mb-0">{{ $leave->reason }}</p>
					</div>
				</div>

				@if($leave->attachment)
					<div class="mb-4">
						<label class="text-muted small">Attachment</label>
						<div class="p-3 bg-light rounded d-flex align-items-center gap-3">
							<i data-feather="paperclip"></i>
							<a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="text-primary">
								View Attachment
							</a>
						</div>
					</div>
				@endif

				@if($leave->admin_remarks)
					<div class="mb-4">
						<label class="text-muted small">Admin Remarks</label>
						<div class="p-3 bg-light-{{ $leave->status === 'approved' ? 'success' : 'danger' }} rounded">
							<p class="mb-0">{{ $leave->admin_remarks }}</p>
						</div>
					</div>
				@endif

				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label class="text-muted small">Applied By</label>
							<p class="mb-0 fw-medium">{{ $leave->appliedByUser->name ?? 'N/A' }}</p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="text-muted small">Applied On</label>
							<p class="mb-0 fw-medium">{{ $leave->created_at->format('M d, Y h:i A') }}</p>
						</div>
					</div>
				</div>
			</div>

			@if($leave->status === 'pending')
				<div class="card-footer">
					<div class="d-flex gap-2 justify-content-end">
						<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
							<i class="fa fa-times me-1"></i> Reject
						</button>
						<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
							<i class="fa fa-check me-1"></i> Approve
						</button>
					</div>
				</div>
			@endif
		</div>

		<!-- Back Button -->
		<div class="d-flex justify-content-start">
			<a href="{{ route('admin.leaves.index') }}" class="btn btn-secondary">
				<i class="fa fa-arrow-left me-1"></i> Back to List
			</a>
		</div>
	</div>
</div>

@if($leave->status === 'pending')
	<!-- Approve Modal -->
	<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="{{ route('admin.leaves.approve', $leave) }}" method="POST">
					@csrf
					<div class="modal-header">
						<h5 class="modal-title" id="approveModalLabel">Approve Leave Application</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<p>You are about to approve the leave application for <strong>{{ $leave->student->first_name ?? '' }} {{ $leave->student->last_name ?? '' }}</strong>.</p>
						<p class="mb-3">
							<strong>Leave Period:</strong> {{ $leave->from_date->format('M d, Y') }} to {{ $leave->to_date->format('M d, Y') }} ({{ $leave->total_days }} day(s))
						</p>
						<div class="mb-3">
							<label for="approve_remarks" class="form-label">Remarks (Optional)</label>
							<textarea name="admin_remarks" id="approve_remarks" class="form-control" rows="3" placeholder="Add any remarks or notes..."></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-success">Approve Application</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Reject Modal -->
	<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="{{ route('admin.leaves.reject', $leave) }}" method="POST">
					@csrf
					<div class="modal-header">
						<h5 class="modal-title" id="rejectModalLabel">Reject Leave Application</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<p>You are about to reject the leave application for <strong>{{ $leave->student->first_name ?? '' }} {{ $leave->student->last_name ?? '' }}</strong>.</p>
						<div class="mb-3">
							<label for="reject_remarks" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
							<textarea name="admin_remarks" id="reject_remarks" class="form-control" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-danger">Reject Application</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endif
@endsection
