@extends('layouts.app')

@section('title', 'Fee Types')

@section('page-title', 'Fee Types')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="#">Fees</a></li>
	<li class="breadcrumb-item active">Fee Types</li>
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

		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Fee Types</h5>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
							<i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
						</button>
						<a href="{{ route('admin.fees.types.trash') }}" class="btn btn-outline-danger position-relative">
							<i data-feather="trash" class="me-1"></i> Trash
							@if($trashedCount > 0)
								<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
									{{ $trashedCount > 99 ? '99+' : $trashedCount }}
								</span>
							@endif
						</a>
						<a href="{{ route('admin.fees.types.create') }}" class="btn btn-primary">
							<i data-feather="plus" class="me-1"></i> Add Fee Type
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-md-4">
						<form action="{{ route('admin.fees.types.index') }}" method="GET">
							<div class="input-group">
								<input type="text" name="search" class="form-control" placeholder="Search by name or code..." value="{{ request('search') }}">
								<button class="btn btn-outline-secondary" type="submit">
									<i data-feather="search"></i>
								</button>
							</div>
						</form>
					</div>
					<div class="col-md-3">
						<form action="{{ route('admin.fees.types.index') }}" method="GET">
							<select name="status" class="form-select" onchange="this.form.submit()">
								<option value="">All Status</option>
								<option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
								<option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
							</select>
						</form>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th style="width: 40px;">
									<input type="checkbox" class="form-check-input" id="selectAll" title="Select All">
								</th>
								<th>#</th>
								<th>Code</th>
								<th>Name</th>
								<th>Description</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($feeTypes as $feeType)
								<tr>
									<td>
										<input type="checkbox" class="form-check-input fee-type-checkbox" value="{{ $feeType->id }}" data-name="{{ $feeType->name }}">
									</td>
									<td>{{ $feeTypes->firstItem() + $loop->index }}</td>
									<td><span class="badge badge-light-primary">{{ $feeType->code }}</span></td>
									<td><strong>{{ $feeType->name }}</strong></td>
									<td>{{ Str::limit($feeType->description, 50) ?? '-' }}</td>
									<td>
										<span class="badge badge-light-{{ $feeType->is_active ? 'success' : 'danger' }}">
											{{ $feeType->is_active ? 'Active' : 'Inactive' }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.fees.types.edit', $feeType) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.fees.types.destroy', $feeType) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $feeType->name }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="dollar-sign" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No fee types found.</p>
											<a href="{{ route('admin.fees.types.create') }}" class="btn btn-primary mt-3">Add First Fee Type</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($feeTypes->hasPages())
					<div class="mt-3">
						{{ $feeTypes->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	const selectAllCheckbox = jQuery('#selectAll');
	const feeTypeCheckboxes = jQuery('.fee-type-checkbox');
	const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
	const selectedCountSpan = jQuery('#selectedCount');

	// Update selected count and toggle bulk delete button
	function updateBulkDeleteState() {
		const checkedCount = jQuery('.fee-type-checkbox:checked').length;
		selectedCountSpan.text(checkedCount);

		if (checkedCount > 0) {
			bulkDeleteBtn.removeClass('d-none');
		} else {
			bulkDeleteBtn.addClass('d-none');
		}

		// Update select all checkbox state
		const totalCheckboxes = feeTypeCheckboxes.length;
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

	// Select All checkbox handler
	selectAllCheckbox.on('change', function() {
		feeTypeCheckboxes.prop('checked', jQuery(this).is(':checked'));
		updateBulkDeleteState();
	});

	// Individual checkbox handler
	feeTypeCheckboxes.on('change', function() {
		updateBulkDeleteState();
	});

	// Bulk Delete button handler
	bulkDeleteBtn.on('click', function() {
		const selectedIds = [];
		const selectedNames = [];

		jQuery('.fee-type-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
			selectedNames.push(jQuery(this).data('name'));
		});

		if (selectedIds.length === 0) {
			Swal.fire({
				icon: 'warning',
				title: 'No Selection',
				text: 'Please select at least one fee type to delete.'
			});
			return;
		}

		const namesText = selectedIds.length <= 5
			? selectedNames.join(', ')
			: selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${selectedIds.length}</strong> fee type(s) to trash:<br><br><small>${namesText}</small><br><br><small class="text-muted">You can restore them later from the trash.</small>`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, move to trash',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ route("admin.fees.types.bulk-delete") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						fee_type_ids: selectedIds
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Moving to Trash...',
							text: 'Please wait while we move the selected fee types to trash.',
							allowOutsideClick: false,
							allowEscapeKey: false,
							didOpen: () => {
								Swal.showLoading();
							}
						});
					},
					success: function(response) {
						Swal.fire({
							icon: 'success',
							title: 'Moved to Trash!',
							text: response.message,
							confirmButtonColor: '#3085d6'
						}).then(() => {
							window.location.reload();
						});
					},
					error: function(xhr) {
						const message = xhr.responseJSON?.message || 'An error occurred.';
						Swal.fire({
							icon: 'error',
							title: 'Error!',
							text: message
						});
					}
				});
			}
		});
	});

	// Move to Trash handler (single delete)
	jQuery(document).on('click', '.move-to-trash', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		var itemName = jQuery(this).data('name') || 'this fee type';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${itemName}</strong> to trash.<br><small class="text-muted">You can restore this fee type later from the trash.</small>`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#FC4438',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, move to trash',
			cancelButtonText: 'Cancel',
			reverseButtons: true
		}).then(function(result) {
			if (result.isConfirmed) {
				form.submit();
			}
		});
	});

	// Re-initialize feather icons if needed
	if (typeof feather !== 'undefined') {
		feather.replace();
	}
});
</script>
@endpush
