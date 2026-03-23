@extends('layouts.app')

@section('title', 'Vehicles')

@section('page-title', 'Transport - Vehicles')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item">Transport</li>
	<li class="breadcrumb-item active">Vehicles</li>
@endsection

@section('content')
<div class="container-fluid">
	<div class="card shadow-sm border-0">
		<div class="card-header bg-white py-3">
			<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
				<h6 class="mb-0 fw-bold">All Vehicles</h6>
				<div class="d-flex gap-2 align-items-center">
					<button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
						<i class="icon-trash me-1"></i> Delete (<span id="selectedCount">0</span>)
					</button>
					<a href="{{ route('admin.transport.vehicles.trash') }}" class="btn btn-outline-danger position-relative">
						<i class="icon-trash me-1"></i> Trash
						@if($trashedCount > 0)
							<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
								{{ $trashedCount > 99 ? '99+' : $trashedCount }}
							</span>
						@endif
					</a>
					<a href="{{ route('admin.transport.vehicles.create') }}" class="btn btn-primary">
						<i class="icon-plus me-1"></i> Add New
					</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
					<i class="icon-check me-1"></i> {{ session('success') }}
					<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
				</div>
			@endif

			@if(session('error'))
				<div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
					<i class="icon-alert me-1"></i> {{ session('error') }}
					<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
				</div>
			@endif

			<!-- Filters -->
			<form action="{{ route('admin.transport.vehicles.index') }}" method="GET" class="row g-3 align-items-end mb-4">
				<div class="col-md-4">
					<label class="form-label">Search</label>
					<input type="text" name="search" class="form-control" placeholder="Vehicle No, Model, Registration, Driver..." value="{{ request('search') }}">
				</div>
				<div class="col-md-3">
					<label class="form-label">Status</label>
					<select name="status" class="form-select">
						<option value="">All Status</option>
						<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
						<option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
						<option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
					</select>
				</div>
				<div class="col-md-3">
					<div class="d-flex gap-2">
						<button type="submit" class="btn btn-primary flex-fill">
							<i class="icon-filter me-1"></i> Filter
						</button>
						@if(request()->hasAny(['search', 'status']))
							<a href="{{ route('admin.transport.vehicles.index') }}" class="btn btn-outline-secondary" title="Reset">
								<i class="icon-reload"></i>
							</a>
						@endif
					</div>
				</div>
			</form>

			<!-- Table -->
			<div class="table-responsive">
				<table class="table table-hover mb-0">
					<thead class="bg-light">
						<tr>
							<th style="width: 40px;">
								<input type="checkbox" class="form-check-input" id="selectAll" title="Select All" autocomplete="off">
							</th>
							<th style="width: 14%;">Vehicle No</th>
							<th style="width: 14%;">Model</th>
							<th style="width: 14%;">Registration</th>
							<th style="width: 20%;">Driver</th>
							<th style="width: 10%;" class="text-center">Capacity</th>
							<th style="width: 10%;" class="text-center">Status</th>
							<th style="width: 10%;" class="text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						@forelse($vehicles as $vehicle)
							<tr>
								<td>
									<input type="checkbox" class="form-check-input vehicle-checkbox" value="{{ $vehicle->id }}" data-name="{{ $vehicle->vehicle_no }}" autocomplete="off">
								</td>
								<td>
									<span class="fw-bold">{{ $vehicle->vehicle_no }}</span>
								</td>
								<td>{{ $vehicle->vehicle_model }}</td>
								<td><span class="text-muted">{{ $vehicle->registration_no }}</span></td>
								<td>
									@if($vehicle->driver_name)
										<div style="line-height: 1.3;">
											<span class="fw-medium">{{ $vehicle->driver_name }}</span>
											@if($vehicle->driver_contact)
												<br><small class="text-muted"><i class="icon-mobile me-1" style="font-size: 11px;"></i>{{ $vehicle->driver_contact }}</small>
											@endif
										</div>
									@else
										<span class="text-muted">Not assigned</span>
									@endif
								</td>
								<td class="text-center">
									<span class="badge badge-light-primary px-2 py-1">{{ $vehicle->max_seating_capacity }} seats</span>
								</td>
								<td class="text-center">
									@if($vehicle->status == 'active')
										<span class="badge badge-light-success px-2">Active</span>
									@elseif($vehicle->status == 'maintenance')
										<span class="badge badge-light-warning px-2">Maintenance</span>
									@else
										<span class="badge badge-light-secondary px-2">Inactive</span>
									@endif
								</td>
								<td class="text-center">
									<div class="common-align gap-2 justify-content-center">
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
								<td colspan="8" class="text-center py-5">
									<div class="d-flex flex-column align-items-center">
										<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
											<i class="icon-car" style="font-size: 24px; color: #95a5a6;"></i>
										</div>
										<p class="text-muted mb-0">No vehicles found.</p>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
		@if($vehicles->hasPages())
			<div class="card-footer bg-white">
				{{ $vehicles->appends(request()->query())->links() }}
			</div>
		@endif
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	// Clear all checkboxes on page load
	jQuery('#selectAll').prop('checked', false).prop('indeterminate', false);
	jQuery('.vehicle-checkbox').prop('checked', false);

	function updateBulkDeleteState() {
		var checkedCount = jQuery('.vehicle-checkbox:checked').length;
		var totalCount = jQuery('.vehicle-checkbox').length;
		jQuery('#selectedCount').text(checkedCount);

		if (checkedCount > 0) {
			jQuery('#bulkDeleteBtn').removeClass('d-none');
		} else {
			jQuery('#bulkDeleteBtn').addClass('d-none');
		}

		if (totalCount > 0 && checkedCount === totalCount) {
			jQuery('#selectAll').prop('checked', true).prop('indeterminate', false);
		} else if (checkedCount > 0) {
			jQuery('#selectAll').prop('checked', false).prop('indeterminate', true);
		} else {
			jQuery('#selectAll').prop('checked', false).prop('indeterminate', false);
		}
	}

	jQuery(document).on('change', '#selectAll', function() {
		jQuery('.vehicle-checkbox').prop('checked', jQuery(this).is(':checked'));
		updateBulkDeleteState();
	});

	jQuery(document).on('change', '.vehicle-checkbox', function() {
		updateBulkDeleteState();
	});

	// Bulk Delete
	jQuery(document).on('click', '#bulkDeleteBtn', function() {
		var selectedIds = [];
		var selectedNames = [];

		jQuery('.vehicle-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
			selectedNames.push(jQuery(this).data('name'));
		});

		if (selectedIds.length === 0) return;

		var namesText = selectedIds.length <= 5
			? selectedNames.join(', ')
			: selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

		Swal.fire({
			title: 'Move to Trash?',
			html: 'You are about to move <strong>' + selectedIds.length + '</strong> vehicle(s) to trash:<br><br><small>' + namesText + '</small><br><br><small class="text-muted">You can restore them later from the trash.</small>',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, move to trash',
			cancelButtonText: 'Cancel'
		}).then(function(result) {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ route("admin.transport.vehicles.bulk-delete") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						ids: selectedIds
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Moving to Trash...',
							allowOutsideClick: false,
							allowEscapeKey: false,
							didOpen: function() { Swal.showLoading(); }
						});
					},
					success: function(response) {
						Swal.fire({
							icon: 'success',
							title: 'Moved to Trash!',
							text: response.message,
							confirmButtonColor: '#3085d6'
						}).then(function() {
							window.location.reload();
						});
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

	// Single Move to Trash
	jQuery(document).on('click', '.move-to-trash', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		var itemName = jQuery(this).data('name') || 'this vehicle';

		Swal.fire({
			title: 'Move to Trash?',
			html: 'You are about to move <strong>' + itemName + '</strong> to trash.<br><small class="text-muted">You can restore it later from the trash.</small>',
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
});
</script>
@endpush
