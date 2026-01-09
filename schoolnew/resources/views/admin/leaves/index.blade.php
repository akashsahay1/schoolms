@extends('layouts.app')

@section('title', 'Leave Applications')
@section('page-title', 'Leave Applications')

@section('breadcrumb')
	<li class="breadcrumb-item">Attendance</li>
	<li class="breadcrumb-item active">Leave Applications</li>
@endsection

@section('content')
<div class="row">
	<!-- Stats Cards -->
	<div class="col-sm-6 col-xl-3">
		<div class="card o-hidden">
			<div class="card-body bg-light-warning">
				<div class="d-flex align-items-center gap-3">
					<div class="flex-shrink-0">
						<div class="bg-warning p-3 rounded-circle">
							<svg class="text-white" style="width: 24px; height: 24px;">
								<use href="{{ asset('assets/svg/icon-sprite.svg#clock') }}"></use>
							</svg>
						</div>
					</div>
					<div class="flex-grow-1">
						<h4 class="mb-0">{{ $pendingCount }}</h4>
						<span class="f-light">Pending</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="card o-hidden">
			<div class="card-body bg-light-success">
				<div class="d-flex align-items-center gap-3">
					<div class="flex-shrink-0">
						<div class="bg-success p-3 rounded-circle">
							<svg class="text-white" style="width: 24px; height: 24px;">
								<use href="{{ asset('assets/svg/icon-sprite.svg#check-circle') }}"></use>
							</svg>
						</div>
					</div>
					<div class="flex-grow-1">
						<h4 class="mb-0">{{ $approvedCount }}</h4>
						<span class="f-light">Approved</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="card o-hidden">
			<div class="card-body bg-light-danger">
				<div class="d-flex align-items-center gap-3">
					<div class="flex-shrink-0">
						<div class="bg-danger p-3 rounded-circle">
							<svg class="text-white" style="width: 24px; height: 24px;">
								<use href="{{ asset('assets/svg/icon-sprite.svg#close') }}"></use>
							</svg>
						</div>
					</div>
					<div class="flex-grow-1">
						<h4 class="mb-0">{{ $rejectedCount }}</h4>
						<span class="f-light">Rejected</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="card o-hidden">
			<div class="card-body bg-light-primary">
				<div class="d-flex align-items-center gap-3">
					<div class="flex-shrink-0">
						<div class="bg-primary p-3 rounded-circle">
							<svg class="text-white" style="width: 24px; height: 24px;">
								<use href="{{ asset('assets/svg/icon-sprite.svg#file-text') }}"></use>
							</svg>
						</div>
					</div>
					<div class="flex-grow-1">
						<h4 class="mb-0">{{ $totalCount }}</h4>
						<span class="f-light">Total</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Leave Applications</h5>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-success btn-sm" id="bulkApproveBtn" style="display: none;">
							<i class="fa fa-check me-1"></i> Approve Selected
						</button>
						<button type="button" class="btn btn-danger btn-sm" id="bulkRejectBtn" style="display: none;">
							<i class="fa fa-times me-1"></i> Reject Selected
						</button>
					</div>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.leaves.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-2">
							<select name="status" class="form-select">
								<option value="">All Status</option>
								@foreach($statuses as $key => $label)
									<option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<select name="class_id" class="form-select">
								<option value="">All Classes</option>
								@foreach($classes as $class)
									<option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<select name="leave_type" class="form-select">
								<option value="">All Types</option>
								@foreach($leaveTypes as $key => $label)
									<option value="{{ $key }}" {{ request('leave_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<input type="date" name="from_date" class="form-control" placeholder="From Date" value="{{ request('from_date') }}">
						</div>
						<div class="col-md-2">
							<input type="date" name="to_date" class="form-control" placeholder="To Date" value="{{ request('to_date') }}">
						</div>
						<div class="col-md-1">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search"></i>
							</button>
						</div>
						@if(request()->hasAny(['status', 'class_id', 'leave_type', 'from_date', 'to_date']))
							<div class="col-md-1">
								<a href="{{ route('admin.leaves.index') }}" class="btn btn-outline-secondary w-100" title="Clear Filters">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Leave Applications Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>
									<input type="checkbox" id="selectAll" class="form-check-input">
								</th>
								<th>Student</th>
								<th>Class</th>
								<th>Leave Type</th>
								<th>From</th>
								<th>To</th>
								<th>Days</th>
								<th>Status</th>
								<th>Applied On</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($leaves as $leave)
								<tr>
									<td>
										@if($leave->status === 'pending')
											<input type="checkbox" class="form-check-input leave-checkbox" value="{{ $leave->id }}">
										@endif
									</td>
									<td>
										<div class="d-flex align-items-center gap-2">
											@if($leave->student && $leave->student->photo)
												<img src="{{ asset('storage/' . $leave->student->photo) }}" alt="" class="rounded-circle" width="35" height="35">
											@else
												<div class="bg-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
													<span class="text-primary">{{ $leave->student ? substr($leave->student->first_name, 0, 1) : '?' }}</span>
												</div>
											@endif
											<div>
												<span class="fw-medium">{{ $leave->student->first_name ?? 'N/A' }} {{ $leave->student->last_name ?? '' }}</span>
												<br><small class="text-muted">{{ $leave->student->admission_no ?? '' }}</small>
											</div>
										</div>
									</td>
									<td>{{ $leave->student->schoolClass->name ?? 'N/A' }} - {{ $leave->student->section->name ?? '' }}</td>
									<td><span class="badge badge-light-info">{{ $leave->getLeaveTypeLabel() }}</span></td>
									<td>{{ $leave->from_date->format('M d, Y') }}</td>
									<td>{{ $leave->to_date->format('M d, Y') }}</td>
									<td><span class="badge badge-light-secondary">{{ $leave->total_days }} day(s)</span></td>
									<td><span class="badge {{ $leave->getStatusBadgeClass() }}">{{ $leave->getStatusLabel() }}</span></td>
									<td>{{ $leave->created_at->format('M d, Y') }}</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.leaves.show', $leave) }}" title="View Details">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											@if($leave->status === 'pending')
												<button type="button" class="square-white border-0 bg-transparent p-0 quick-approve-btn" data-id="{{ $leave->id }}" title="Quick Approve">
													<svg class="text-success"><use href="{{ asset('assets/svg/icon-sprite.svg#check-circle') }}"></use></svg>
												</button>
												<button type="button" class="square-white border-0 bg-transparent p-0 quick-reject-btn" data-id="{{ $leave->id }}" title="Reject">
													<svg class="text-danger"><use href="{{ asset('assets/svg/icon-sprite.svg#close') }}"></use></svg>
												</button>
											@endif
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="10" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="file-text" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No leave applications found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
				@if($leaves->hasPages())
					<div class="mt-3">
						{{ $leaves->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

<!-- Quick Approve Form -->
<form id="quickApproveForm" action="" method="POST" style="display: none;">
	@csrf
</form>

<!-- Quick Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="rejectForm" action="" method="POST">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title" id="rejectModalLabel">Reject Leave Application</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label for="admin_remarks" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
						<textarea name="admin_remarks" id="admin_remarks" class="form-control" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
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

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1" aria-labelledby="bulkRejectModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="bulkRejectForm" action="{{ route('admin.leaves.bulk-reject') }}" method="POST">
				@csrf
				<input type="hidden" name="leave_ids" id="bulkRejectIds">
				<div class="modal-header">
					<h5 class="modal-title" id="bulkRejectModalLabel">Reject Selected Applications</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>You are about to reject <strong id="bulkRejectCount">0</strong> leave application(s).</p>
					<div class="mb-3">
						<label for="bulk_admin_remarks" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
						<textarea name="admin_remarks" id="bulk_admin_remarks" class="form-control" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger">Reject All</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Bulk Approve Form -->
<form id="bulkApproveForm" action="{{ route('admin.leaves.bulk-approve') }}" method="POST" style="display: none;">
	@csrf
	<input type="hidden" name="leave_ids" id="bulkApproveIds">
</form>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	// Select All Checkbox
	jQuery('#selectAll').on('change', function() {
		jQuery('.leave-checkbox').prop('checked', jQuery(this).prop('checked'));
		toggleBulkButtons();
	});

	// Individual Checkbox
	jQuery('.leave-checkbox').on('change', function() {
		toggleBulkButtons();
	});

	function toggleBulkButtons() {
		var checkedCount = jQuery('.leave-checkbox:checked').length;
		if (checkedCount > 0) {
			jQuery('#bulkApproveBtn, #bulkRejectBtn').show();
		} else {
			jQuery('#bulkApproveBtn, #bulkRejectBtn').hide();
		}
	}

	// Quick Approve
	jQuery('.quick-approve-btn').on('click', function() {
		var leaveId = jQuery(this).data('id');
		Swal.fire({
			title: 'Approve Leave?',
			text: 'Are you sure you want to approve this leave application?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#54BA4A',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, Approve'
		}).then(function(result) {
			if (result.isConfirmed) {
				jQuery('#quickApproveForm').attr('action', '/admin/leaves/' + leaveId + '/approve');
				jQuery('#quickApproveForm').submit();
			}
		});
	});

	// Quick Reject
	jQuery('.quick-reject-btn').on('click', function() {
		var leaveId = jQuery(this).data('id');
		jQuery('#rejectForm').attr('action', '/admin/leaves/' + leaveId + '/reject');
		jQuery('#rejectModal').modal('show');
	});

	// Bulk Approve
	jQuery('#bulkApproveBtn').on('click', function() {
		var selectedIds = [];
		jQuery('.leave-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
		});

		Swal.fire({
			title: 'Approve All Selected?',
			text: 'Are you sure you want to approve ' + selectedIds.length + ' leave application(s)?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#54BA4A',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, Approve All'
		}).then(function(result) {
			if (result.isConfirmed) {
				jQuery('#bulkApproveIds').val(JSON.stringify(selectedIds));
				jQuery('#bulkApproveForm').submit();
			}
		});
	});

	// Bulk Reject
	jQuery('#bulkRejectBtn').on('click', function() {
		var selectedIds = [];
		jQuery('.leave-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
		});
		jQuery('#bulkRejectIds').val(JSON.stringify(selectedIds));
		jQuery('#bulkRejectCount').text(selectedIds.length);
		jQuery('#bulkRejectModal').modal('show');
	});
});
</script>
@endpush
