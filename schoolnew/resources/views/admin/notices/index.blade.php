@extends('layouts.app')

@section('title', 'Notices')
@section('page-title', 'Notices')

@section('breadcrumb')
	<li class="breadcrumb-item">Communication</li>
	<li class="breadcrumb-item active">Notices</li>
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
					<h5>All Notices</h5>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
							<i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
						</button>
						<a href="{{ route('admin.notices.trash') }}" class="btn btn-outline-danger position-relative">
							<i data-feather="trash" class="me-1"></i> Trash
							@if($trashedCount > 0)
								<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
									{{ $trashedCount > 99 ? '99+' : $trashedCount }}
								</span>
							@endif
						</a>
						<a href="{{ route('admin.notices.create') }}" class="btn btn-primary">
							<i class="fa fa-plus me-1"></i> Add Notice
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.notices.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<select name="type" class="form-select">
								<option value="">All Types</option>
								@foreach(\App\Models\Notice::TYPES as $key => $label)
									<option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<select name="status" class="form-select">
								<option value="">All Status</option>
								<option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
								<option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
								<option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Filter
							</button>
						</div>
						@if(request()->hasAny(['type', 'status']))
							<div class="col-md-1">
								<a href="{{ route('admin.notices.index') }}" class="btn btn-outline-secondary w-100" title="Clear Filters">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Notices Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th style="width: 40px;">
									<input type="checkbox" class="form-check-input" id="selectAll" title="Select All">
								</th>
								<th>Title</th>
								<th>Type</th>
								<th>Publish Date</th>
								<th>Expiry</th>
								<th>Status</th>
								<th>Created By</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($notices as $notice)
								<tr>
									<td>
										<input type="checkbox" class="form-check-input notice-checkbox" value="{{ $notice->id }}" data-name="{{ $notice->title }}">
									</td>
									<td>{{ Str::limit($notice->title, 40) }}</td>
									<td><span class="badge {{ $notice->getTypeBadgeClass() }}">{{ $notice->getTypeLabel() }}</span></td>
									<td>{{ $notice->publish_date->format('M d, Y') }}</td>
									<td>{{ $notice->expiry_date ? $notice->expiry_date->format('M d, Y') : 'No Expiry' }}</td>
									<td>
										@if(!$notice->is_published)
											<span class="badge badge-light-secondary">Draft</span>
										@elseif($notice->isExpired())
											<span class="badge badge-light-danger">Expired</span>
										@else
											<span class="badge badge-light-success">Published</span>
										@endif
									</td>
									<td>{{ $notice->creator->name ?? 'N/A' }}</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.notices.show', $notice) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.notices.edit', $notice) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $notice->title }}">
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
											<i data-feather="bell" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No notices found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
				@if($notices->hasPages())
					<div class="mt-3">
						{{ $notices->links() }}
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
	const noticeCheckboxes = jQuery('.notice-checkbox');
	const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
	const selectedCountSpan = jQuery('#selectedCount');

	// Update selected count and toggle bulk delete button
	function updateBulkDeleteState() {
		const checkedCount = jQuery('.notice-checkbox:checked').length;
		selectedCountSpan.text(checkedCount);

		if (checkedCount > 0) {
			bulkDeleteBtn.removeClass('d-none');
		} else {
			bulkDeleteBtn.addClass('d-none');
		}

		// Update select all checkbox state
		const totalCheckboxes = noticeCheckboxes.length;
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
		noticeCheckboxes.prop('checked', jQuery(this).is(':checked'));
		updateBulkDeleteState();
	});

	// Individual checkbox handler
	noticeCheckboxes.on('change', function() {
		updateBulkDeleteState();
	});

	// Bulk Delete button handler
	bulkDeleteBtn.on('click', function() {
		const selectedIds = [];
		const selectedNames = [];

		jQuery('.notice-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
			selectedNames.push(jQuery(this).data('name'));
		});

		if (selectedIds.length === 0) {
			Swal.fire({
				icon: 'warning',
				title: 'No Selection',
				text: 'Please select at least one notice to delete.'
			});
			return;
		}

		const namesText = selectedIds.length <= 5
			? selectedNames.join(', ')
			: selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${selectedIds.length}</strong> notice(s) to trash:<br><br><small>${namesText}</small><br><br><small class="text-muted">You can restore them later from the trash.</small>`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, move to trash',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ route("admin.notices.bulk-delete") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						notice_ids: selectedIds
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Moving to Trash...',
							text: 'Please wait while we move the selected notices to trash.',
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
		var itemName = jQuery(this).data('name') || 'this notice';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${itemName}</strong> to trash.<br><small class="text-muted">You can restore this notice later from the trash.</small>`,
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
