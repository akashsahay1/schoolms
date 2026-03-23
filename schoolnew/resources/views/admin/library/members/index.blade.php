@extends('layouts.app')

@section('title', 'Library Members')

@section('page-title', 'Library Members')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.library.books.index') }}">Library</a></li>
	<li class="breadcrumb-item active">Members</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<!-- Success/Error Messages -->
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

		<!-- Statistics Cards -->
		<div class="row mb-4">
			<div class="col-md-3">
				<div class="card bg-light-primary">
					<div class="card-body text-center py-3">
						<h3 class="mb-1">{{ $stats['total'] }}</h3>
						<p class="mb-0 text-muted">Total Members</p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card bg-light-success">
					<div class="card-body text-center py-3">
						<h3 class="mb-1">{{ $stats['active'] }}</h3>
						<p class="mb-0 text-muted">Active</p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card bg-light-warning">
					<div class="card-body text-center py-3">
						<h3 class="mb-1">{{ $stats['expired'] }}</h3>
						<p class="mb-0 text-muted">Expired</p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card bg-light-danger">
					<div class="card-body text-center py-3">
						<h3 class="mb-1">{{ $stats['suspended'] }}</h3>
						<p class="mb-0 text-muted">Suspended</p>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Library Members</h5>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
							<i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
						</button>
						<a href="{{ route('admin.library.members.trash') }}" class="btn btn-outline-danger position-relative">
							<i data-feather="trash" class="me-1"></i> Trash
							@if($trashedCount > 0)
								<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
									{{ $trashedCount > 99 ? '99+' : $trashedCount }}
								</span>
							@endif
						</a>
						<a href="{{ route('admin.library.members.create') }}" class="btn btn-primary">
							<i data-feather="plus" class="me-1"></i> Add New
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.library.members.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<input type="text" name="search" class="form-control" placeholder="Search by ID, name..." value="{{ request('search') }}">
						</div>
						<div class="col-md-2">
							<select name="type" class="form-select">
								<option value="">All Types</option>
								<option value="student" {{ request('type') == 'student' ? 'selected' : '' }}>Students</option>
								<option value="staff" {{ request('type') == 'staff' ? 'selected' : '' }}>Staff</option>
							</select>
						</div>
						<div class="col-md-2">
							<select name="status" class="form-select">
								<option value="">All Status</option>
								<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
								<option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
								<option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i class="icon-filter me-1"></i> Filter
							</button>
						</div>
						@if(request()->hasAny(['search', 'type', 'status']))
							<div class="col-md-2">
								<a href="{{ route('admin.library.members.index') }}" class="btn btn-outline-secondary w-100">
									<i data-feather="x" class="me-1"></i> Clear
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Members Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th style="width: 40px;">
									<input type="checkbox" class="form-check-input" id="selectAll" title="Select All">
								</th>
								<th>#</th>
								<th>Member ID</th>
								<th>Name</th>
								<th>Type</th>
								<th>Books</th>
								<th>Validity</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($members as $member)
								<tr>
									<td>
										<input type="checkbox" class="form-check-input member-checkbox" value="{{ $member->id }}" data-name="{{ $member->member_id }}">
									</td>
									<td>{{ $members->firstItem() + $loop->index }}</td>
									<td>
										<span class="fw-semibold">{{ $member->member_id }}</span>
									</td>
									<td>
										<div class="d-flex align-items-center">
											@if($member->memberable)
												<img src="{{ $member->memberable->photo_url ?? asset('assets/images/user/user.png') }}" alt="Photo" class="rounded-circle me-2" width="32" height="32">
												<div>
													<div class="fw-semibold">{{ $member->member_name }}</div>
													@if($member->memberable_type === 'App\Models\Student')
														<small class="text-muted">{{ $member->memberable->admission_no ?? '' }}</small>
													@else
														<small class="text-muted">{{ $member->memberable->staff_id ?? '' }}</small>
													@endif
												</div>
											@else
												<span class="text-muted">Member deleted</span>
											@endif
										</div>
									</td>
									<td>
										<span class="badge bg-light-{{ $member->member_type === 'Student' ? 'info' : 'primary' }}">
											{{ $member->member_type }}
										</span>
									</td>
									<td>
										<span class="badge bg-light-{{ $member->current_books_count > 0 ? 'warning' : 'secondary' }}">
											{{ $member->current_books_count }}/{{ $member->max_books_allowed }}
										</span>
									</td>
									<td>
										@if($member->membership_end)
											<span class="{{ $member->is_expired ? 'text-danger' : 'text-success' }}">
												{{ $member->membership_end->format('M d, Y') }}
											</span>
										@else
											<span class="text-muted">Lifetime</span>
										@endif
									</td>
									<td>
										@if($member->status === 'active')
											<span class="badge bg-success">Active</span>
										@elseif($member->status === 'expired')
											<span class="badge bg-warning">Expired</span>
										@else
											<span class="badge bg-danger">Suspended</span>
										@endif
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.library.members.show', $member) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.library.members.card', $member) }}" title="Print Card" target="_blank">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#printer') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.library.members.edit', $member) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.library.members.destroy', $member) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $member->member_id }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="9" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="users" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No library members found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-center mt-4">
					{{ $members->withQueryString()->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	const selectAllCheckbox = jQuery('#selectAll');
	const memberCheckboxes = jQuery('.member-checkbox');
	const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
	const selectedCountSpan = jQuery('#selectedCount');

	function updateBulkDeleteState() {
		const checkedCount = jQuery('.member-checkbox:checked').length;
		selectedCountSpan.text(checkedCount);

		if (checkedCount > 0) {
			bulkDeleteBtn.removeClass('d-none');
		} else {
			bulkDeleteBtn.addClass('d-none');
		}

		const totalCheckboxes = memberCheckboxes.length;
		if (totalCheckboxes > 0 && checkedCount === totalCheckboxes) {
			selectAllCheckbox.prop('checked', true);
			selectAllCheckbox.prop('indeterminate', false);
		} else if (checkedCount > 0) {
			selectAllCheckbox.prop('checked', false);
			selectAllCheckbox.prop('indeterminate', true);
		} else {
			selectAllCheckbox.prop('checked', false);
			selectAllCheckbox.prop('indeterminate', false);
		}
	}

	selectAllCheckbox.on('change', function() {
		memberCheckboxes.prop('checked', jQuery(this).is(':checked'));
		updateBulkDeleteState();
	});

	memberCheckboxes.on('change', function() {
		updateBulkDeleteState();
	});

	bulkDeleteBtn.on('click', function() {
		const selectedIds = [];
		const selectedNames = [];

		jQuery('.member-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
			selectedNames.push(jQuery(this).data('name'));
		});

		if (selectedIds.length === 0) {
			Swal.fire({
				icon: 'warning',
				title: 'No Selection',
				text: 'Please select at least one member to delete.'
			});
			return;
		}

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${selectedIds.length}</strong> member(s) to trash.`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, move to trash',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ route("admin.library.members.bulk-delete") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						member_ids: selectedIds
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Processing...',
							allowOutsideClick: false,
							didOpen: () => { Swal.showLoading(); }
						});
					},
					success: function(response) {
						Swal.fire({
							icon: 'success',
							title: 'Done!',
							text: response.message
						}).then(() => { window.location.reload(); });
					},
					error: function(xhr) {
						Swal.fire({
							icon: 'error',
							title: 'Error!',
							text: xhr.responseJSON?.message || 'An error occurred.'
						});
					}
				});
			}
		});
	});

	jQuery(document).on('click', '.move-to-trash', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		var itemName = jQuery(this).data('name');

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move membership <strong>${itemName}</strong> to trash.`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#FC4438',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, move to trash',
			reverseButtons: true
		}).then(function(result) {
			if (result.isConfirmed) {
				form.submit();
			}
		});
	});

	if (typeof feather !== 'undefined') {
		feather.replace();
	}
});
</script>
@endpush
