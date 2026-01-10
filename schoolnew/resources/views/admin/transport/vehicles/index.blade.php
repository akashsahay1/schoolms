@extends('layouts.app')

@section('title', 'Vehicles')

@section('page-title', 'Transport - Vehicles')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.transport.vehicles.index') }}">Transport</a></li>
	<li class="breadcrumb-item active">Vehicles</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Vehicles List</h5>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
							<i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
						</button>
						<a href="{{ route('admin.transport.vehicles.trash') }}" class="btn btn-outline-danger position-relative">
							<i data-feather="trash" class="me-1"></i> Trash
							@if($trashedCount > 0)
								<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
									{{ $trashedCount > 99 ? '99+' : $trashedCount }}
								</span>
							@endif
						</a>
						<a href="{{ route('admin.transport.vehicles.create') }}" class="btn btn-primary btn-sm">
							<i data-feather="plus" class="me-1"></i> Add Vehicle
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				@if(session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ session('success') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				@if(session('error'))
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						{{ session('error') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				<div class="row mb-3">
					<div class="col-md-4">
						<form action="{{ route('admin.transport.vehicles.index') }}" method="GET">
							<div class="input-group">
								<select name="status" class="form-select">
									<option value="">All Status</option>
									<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
									<option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
									<option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
								</select>
								<button type="submit" class="btn btn-primary">Filter</button>
							</div>
						</form>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th style="width: 40px;">
									<input type="checkbox" class="form-check-input" id="selectAll" title="Select All">
								</th>
								<th>Vehicle No</th>
								<th>Model</th>
								<th>Registration No</th>
								<th>Driver</th>
								<th>Capacity</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($vehicles as $vehicle)
								<tr>
									<td>
										<input type="checkbox" class="form-check-input vehicle-checkbox" value="{{ $vehicle->id }}" data-name="{{ $vehicle->vehicle_no }}">
									</td>
									<td>{{ $vehicle->vehicle_no }}</td>
									<td>{{ $vehicle->vehicle_model }}</td>
									<td>{{ $vehicle->registration_no }}</td>
									<td>
										@if($vehicle->driver_name)
											{{ $vehicle->driver_name }}<br>
											<small class="text-muted">{{ $vehicle->driver_contact }}</small>
										@else
											<span class="text-muted">-</span>
										@endif
									</td>
									<td>{{ $vehicle->max_seating_capacity }} seats</td>
									<td>
										@if($vehicle->status == 'active')
											<span class="badge bg-success">Active</span>
										@elseif($vehicle->status == 'maintenance')
											<span class="badge bg-warning">Maintenance</span>
										@else
											<span class="badge bg-secondary">Inactive</span>
										@endif
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.transport.vehicles.edit', $vehicle) }}" title="Edit">
												<svg>
													<use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use>
												</svg>
											</a>
											<form action="{{ route('admin.transport.vehicles.destroy', $vehicle) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $vehicle->vehicle_no }}">
													<svg>
														<use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use>
													</svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center text-muted py-4">No vehicles found.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<div class="mt-3">
					{{ $vehicles->links() }}
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
	const vehicleCheckboxes = jQuery('.vehicle-checkbox');
	const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
	const selectedCountSpan = jQuery('#selectedCount');

	// Update selected count and toggle bulk delete button
	function updateBulkDeleteState() {
		const checkedCount = jQuery('.vehicle-checkbox:checked').length;
		selectedCountSpan.text(checkedCount);

		if (checkedCount > 0) {
			bulkDeleteBtn.removeClass('d-none');
		} else {
			bulkDeleteBtn.addClass('d-none');
		}

		// Update select all checkbox state
		const totalCheckboxes = vehicleCheckboxes.length;
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
		vehicleCheckboxes.prop('checked', jQuery(this).is(':checked'));
		updateBulkDeleteState();
	});

	// Individual checkbox handler
	vehicleCheckboxes.on('change', function() {
		updateBulkDeleteState();
	});

	// Bulk Delete button handler
	bulkDeleteBtn.on('click', function() {
		const selectedIds = [];
		const selectedNames = [];

		jQuery('.vehicle-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
			selectedNames.push(jQuery(this).data('name'));
		});

		if (selectedIds.length === 0) {
			Swal.fire({
				icon: 'warning',
				title: 'No Selection',
				text: 'Please select at least one vehicle to delete.'
			});
			return;
		}

		const namesText = selectedIds.length <= 5
			? selectedNames.join(', ')
			: selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${selectedIds.length}</strong> vehicle(s) to trash:<br><br><small>${namesText}</small><br><br><small class="text-muted">You can restore them later from the trash.</small>`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, move to trash',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ route("admin.transport.vehicles.bulk-delete") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						vehicle_ids: selectedIds
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Moving to Trash...',
							text: 'Please wait while we move the selected vehicles to trash.',
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
		var itemName = jQuery(this).data('name') || 'this vehicle';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${itemName}</strong> to trash.<br><small class="text-muted">You can restore this vehicle later from the trash.</small>`,
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
