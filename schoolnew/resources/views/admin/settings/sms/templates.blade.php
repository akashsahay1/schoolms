@extends('layouts.app')

@section('title', 'SMS Templates')

@section('page-title', 'Settings - SMS Templates')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.settings.sms.index') }}">SMS Settings</a></li>
	<li class="breadcrumb-item active">Templates</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
					<h5 class="mb-0">SMS Templates</h5>
					<div class="d-flex gap-2 align-items-center">
						<button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn" style="color: #fff;">
							<i class="icon-trash me-1" style="color: #fff;"></i> Delete (<span id="selectedCount" style="color: #fff;">0</span>)
						</button>
						<a href="{{ route('admin.settings.sms.templates.create') }}" class="btn btn-primary">
							<i class="icon-plus me-1"></i> Add Template
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
				<form action="{{ route('admin.settings.sms.templates') }}" method="GET" class="row g-3 align-items-end mb-4">
					<div class="col-md-4">
						<label class="form-label">Search</label>
						<input type="text" name="search" class="form-control" placeholder="Search templates..." value="{{ request('search') }}">
					</div>
					<div class="col-md-3">
						<label class="form-label">Category</label>
						<select name="category" class="form-select">
							<option value="">All Categories</option>
							@foreach($categories as $key => $label)
								<option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<div class="d-flex gap-2">
							<button type="submit" class="btn btn-primary flex-fill">
								<i class="icon-filter me-1"></i> Filter
							</button>
							@if(request()->hasAny(['search', 'category']))
								<a href="{{ route('admin.settings.sms.templates') }}" class="btn btn-outline-secondary" title="Reset">
									<i class="icon-reload"></i>
								</a>
							@endif
						</div>
					</div>
				</form>

				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="bg-light">
							<tr>
								<th style="width: 40px;">
									<input type="checkbox" class="form-check-input" id="selectAll" title="Select All" autocomplete="off">
								</th>
								<th>#</th>
								<th>Name</th>
								<th>Category</th>
								<th>Content</th>
								<th class="text-center">Status</th>
								<th style="width: 100px;" class="text-center">Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($templates as $template)
								<tr>
									<td>
										<input type="checkbox" class="form-check-input template-checkbox" value="{{ $template->id }}" data-name="{{ $template->name }}" autocomplete="off">
									</td>
									<td>{{ $templates->firstItem() + $loop->index }}</td>
									<td>
										<strong>{{ $template->name }}</strong>
										<br><small class="text-muted">{{ $template->slug }}</small>
									</td>
									<td><span class="badge badge-light-primary">{{ $template->category_label }}</span></td>
									<td>
										<small class="text-muted">{{ Str::limit($template->content, 100) }}</small>
									</td>
									<td class="text-center">
										@if($template->is_active)
											<span class="badge badge-light-success">Active</span>
										@else
											<span class="badge badge-light-danger">Inactive</span>
										@endif
									</td>
									<td class="text-center">
										<div class="common-align gap-2 justify-content-center">
											<a class="square-white" href="{{ route('admin.settings.sms.templates.edit', $template) }}" title="Edit">
												<svg>
													<use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use>
												</svg>
											</a>
											<form action="{{ route('admin.settings.sms.templates.destroy', $template) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Delete" data-name="{{ $template->name }}">
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
									<td colspan="7" class="text-center py-5">
										<div class="d-flex flex-column align-items-center">
											<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
												<i class="icon-email" style="font-size: 24px; color: #95a5a6;"></i>
											</div>
											<p class="text-muted mb-0">No templates found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer bg-white">
				@include('components.pagination-info', ['paginator' => $templates])
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	// Clear all checkboxes on page load
	jQuery('#selectAll').prop('checked', false).prop('indeterminate', false);
	jQuery('.template-checkbox').prop('checked', false);

	function updateBulkDeleteState() {
		var checkedCount = jQuery('.template-checkbox:checked').length;
		var totalCount = jQuery('.template-checkbox').length;
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
		jQuery('.template-checkbox').prop('checked', jQuery(this).is(':checked'));
		updateBulkDeleteState();
	});

	jQuery(document).on('change', '.template-checkbox', function() {
		updateBulkDeleteState();
	});

	// Bulk Delete
	jQuery(document).on('click', '#bulkDeleteBtn', function() {
		var selectedIds = [];
		var selectedNames = [];

		jQuery('.template-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
			selectedNames.push(jQuery(this).data('name'));
		});

		if (selectedIds.length === 0) return;

		var namesText = selectedIds.length <= 5
			? selectedNames.join(', ')
			: selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

		Swal.fire({
			title: 'Delete Templates?',
			html: 'You are about to delete <strong>' + selectedIds.length + '</strong> template(s):<br><br><small>' + namesText + '</small><br><br><small class="text-muted">This action cannot be undone.</small>',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, delete them',
			cancelButtonText: 'Cancel'
		}).then(function(result) {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ route("admin.settings.sms.templates.bulk-delete") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						ids: selectedIds
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Deleting...',
							allowOutsideClick: false,
							allowEscapeKey: false,
							didOpen: function() { Swal.showLoading(); }
						});
					},
					success: function(response) {
						Swal.fire({
							icon: 'success',
							title: 'Deleted!',
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

	// Single Delete
	jQuery(document).on('click', '.move-to-trash', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		var itemName = jQuery(this).data('name') || 'this template';

		Swal.fire({
			title: 'Delete Template?',
			html: 'You are about to delete <strong>' + itemName + '</strong>.<br><small class="text-muted">This action cannot be undone.</small>',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#FC4438',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, delete it',
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
