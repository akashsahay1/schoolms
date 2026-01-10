@extends('layouts.app')

@section('title', 'Homework')

@section('page-title', 'Homework Management')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Homework</li>
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
					<h5>All Homework</h5>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
							<i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
						</button>
						<a href="{{ route('admin.homework.trash') }}" class="btn btn-outline-danger position-relative">
							<i data-feather="trash" class="me-1"></i> Trash
							@if($trashedCount > 0)
								<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
									{{ $trashedCount > 99 ? '99+' : $trashedCount }}
								</span>
							@endif
						</a>
						<a href="{{ route('admin.homework.create') }}" class="btn btn-primary">
							<i data-feather="plus" class="me-1"></i> Assign Homework
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<form action="{{ route('admin.homework.index') }}" method="GET" class="row g-3 mb-3">
					<div class="col-md-3">
						<select name="academic_year" class="form-select" onchange="this.form.submit()">
							<option value="">All Academic Years</option>
							@foreach($academicYears as $year)
								<option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<select name="class" class="form-select" onchange="this.form.submit()">
							<option value="">All Classes</option>
							@foreach($classes as $class)
								<option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<select name="subject" class="form-select" onchange="this.form.submit()">
							<option value="">All Subjects</option>
							@foreach($subjects as $subject)
								<option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<select name="status" class="form-select" onchange="this.form.submit()">
							<option value="">All Status</option>
							<option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
							<option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
						</select>
					</div>
				</form>

				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th style="width: 40px;">
									<input type="checkbox" class="form-check-input" id="selectAll" title="Select All">
								</th>
								<th>#</th>
								<th>Title</th>
								<th>Class</th>
								<th>Subject</th>
								<th>Homework Date</th>
								<th>Submission Date</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($homeworks as $homework)
								<tr>
									<td>
										<input type="checkbox" class="form-check-input item-checkbox" value="{{ $homework->id }}" data-name="{{ $homework->title }}">
									</td>
									<td>{{ $homeworks->firstItem() + $loop->index }}</td>
									<td><strong>{{ $homework->title }}</strong></td>
									<td>
										<span class="badge badge-light-info">
											{{ $homework->schoolClass->name }}{{ $homework->section ? ' (' . $homework->section->name . ')' : '' }}
										</span>
									</td>
									<td>{{ $homework->subject->name }}</td>
									<td>{{ $homework->homework_date->format('d M Y') }}</td>
									<td>{{ $homework->submission_date->format('d M Y') }}</td>
									<td>
										@if($homework->is_overdue)
											<span class="badge badge-light-danger">Overdue</span>
										@else
											<span class="badge badge-light-success">Active</span>
										@endif
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.homework.submissions', $homework) }}" title="View Submissions">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.homework.edit', $homework) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.homework.destroy', $homework) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $homework->title }}">
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
											<i data-feather="book-open" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No homework found.</p>
											<a href="{{ route('admin.homework.create') }}" class="btn btn-primary mt-3">Assign First Homework</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($homeworks->hasPages())
					<div class="mt-3">
						{{ $homeworks->links() }}
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
	const itemCheckboxes = jQuery('.item-checkbox');
	const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
	const selectedCountSpan = jQuery('#selectedCount');

	function updateBulkDeleteState() {
		const checkedCount = jQuery('.item-checkbox:checked').length;
		selectedCountSpan.text(checkedCount);

		if (checkedCount > 0) {
			bulkDeleteBtn.removeClass('d-none');
		} else {
			bulkDeleteBtn.addClass('d-none');
		}

		const totalCheckboxes = itemCheckboxes.length;
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
		itemCheckboxes.prop('checked', jQuery(this).is(':checked'));
		updateBulkDeleteState();
	});

	itemCheckboxes.on('change', function() {
		updateBulkDeleteState();
	});

	bulkDeleteBtn.on('click', function() {
		const selectedIds = [];
		const selectedNames = [];

		jQuery('.item-checkbox:checked').each(function() {
			selectedIds.push(jQuery(this).val());
			selectedNames.push(jQuery(this).data('name'));
		});

		if (selectedIds.length === 0) {
			Swal.fire({
				icon: 'warning',
				title: 'No Selection',
				text: 'Please select at least one homework to delete.'
			});
			return;
		}

		const namesText = selectedIds.length <= 5
			? selectedNames.join(', ')
			: selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${selectedIds.length}</strong> homework(s) to trash:<br><br><small>${namesText}</small><br><br><small class="text-muted">You can restore them later from the trash.</small>`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, move to trash',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ route("admin.homework.bulk-delete") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						homework_ids: selectedIds
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Moving to Trash...',
							text: 'Please wait while we move the selected homework to trash.',
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

	jQuery(document).on('click', '.move-to-trash', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		var itemName = jQuery(this).data('name') || 'this homework';

		Swal.fire({
			title: 'Move to Trash?',
			html: `You are about to move <strong>${itemName}</strong> to trash.<br><small class="text-muted">You can restore this homework later from the trash.</small>`,
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

	if (typeof feather !== 'undefined') {
		feather.replace();
	}
});
</script>
@endpush
