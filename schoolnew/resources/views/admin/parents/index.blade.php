@extends('layouts.app')

@section('title', 'Parents')

@section('page-title', 'Parents')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Parents</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<!-- Info Alert -->
		<div class="alert alert-info alert-dismissible fade show" role="alert">
			<i data-feather="info" class="me-2"></i>
			Parents are automatically created when adding students. To add or update parent information, please edit the respective student's record.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>

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

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Parents/Guardians</h5>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
							<i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
						</button>
						<a href="{{ route('admin.parents.trash') }}" class="btn btn-outline-danger position-relative">
							<i data-feather="trash" class="me-1"></i> Trash
							@if($trashedCount > 0)
								<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
									{{ $trashedCount > 99 ? '99+' : $trashedCount }}
								</span>
							@endif
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.parents.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-4">
							<input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Search
							</button>
						</div>
						@if(request()->hasAny(['search']))
							<div class="col-md-1">
								<a href="{{ route('admin.parents.index') }}" class="btn btn-outline-secondary w-100" title="Clear Search">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Parents Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th style="width: 40px;">
									<input type="checkbox" class="form-check-input" id="selectAll" title="Select All">
								</th>
								<th>#</th>
								<th>Father's Name</th>
								<th>Mother's Name</th>
								<th>Contact</th>
								<th>Email</th>
								<th>Children</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($parents as $parent)
								<tr>
									<td>
										<input type="checkbox" class="form-check-input parent-checkbox" value="{{ $parent->id }}" data-name="{{ $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? 'Parent' }}">
									</td>
									<td>{{ $parents->firstItem() + $loop->index }}</td>
									<td>{{ $parent->father_name ?? 'N/A' }}</td>
									<td>{{ $parent->mother_name ?? 'N/A' }}</td>
									<td>{{ $parent->primary_contact ?: 'N/A' }}</td>
									<td>{{ $parent->primary_email ?: 'N/A' }}</td>
									<td>
										@if($parent->students->count() > 0)
											<span class="badge badge-light-primary">{{ $parent->students->count() }} student(s)</span>
										@else
											<span class="text-muted">None</span>
										@endif
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.parents.show', $parent) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<form action="{{ route('admin.parents.destroy', $parent) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? 'Parent' }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="users" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No parents found.</p>
											<p class="text-muted small">Parents are created automatically when you add students.</p>
											<a href="{{ route('admin.students.create') }}" class="btn btn-primary mt-3">Add a Student</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-center mt-4">
					{{ $parents->withQueryString()->links() }}
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
	const parentCheckboxes = jQuery('.parent-checkbox');
	const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
	const selectedCountSpan = jQuery('#selectedCount');

	// Update selected count and toggle bulk delete button
	function updateBulkDeleteState() {
		const checkedCount = jQuery('.parent-checkbox:checked').length;
		selectedCountSpan.text(checkedCount);

		if (checkedCount > 0) {
			bulkDeleteBtn.removeClass('d-none');
		} else {
			bulkDeleteBtn.addClass('d-none');
		}

		// Update select all checkbox state
		const totalCheckboxes = parentCheckboxes.length;
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
		parentCheckboxes.prop('checked', jQuery(this).is(':checked'));
		updateBulkDeleteState();
	});

	// Individual checkbox handler
	parentCheckboxes.on('change', function() {
		updateBulkDeleteState();
	});

	// Bulk Delete button handler
	bulkDeleteBtn.on('click', function() {
		const selectedIds = [];
		const selectedNames = [];

		jQuery('.parent-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
			selectedNames.push(jQuery(this).data('name'));
		});

		if (selectedIds.length === 0) {
			Swal.fire({
				icon: 'warning',
				title: 'No Selection',
				text: 'Please select at least one parent to delete.'
			});
			return;
		}

		const namesText = selectedIds.length <= 5
			? selectedNames.join(', ')
			: selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${selectedIds.length}</strong> parent(s) to trash:<br><br><small>${namesText}</small><br><br><small class="text-muted">You can restore them later from the trash.</small>`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, move to trash',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ route("admin.parents.bulk-delete") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						parent_ids: selectedIds
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Moving to Trash...',
							text: 'Please wait while we move the selected parents to trash.',
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
		var itemName = jQuery(this).data('name') || 'this parent';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${itemName}</strong> to trash.<br><small class="text-muted">You can restore this parent later from the trash.</small>`,
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
