@extends('layouts.app')

@section('title', 'Fee Discounts')

@section('page-title', 'Fee Discounts')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="#">Fees</a></li>
	<li class="breadcrumb-item active">Discounts</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
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

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Fee Discounts</h5>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
							<i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
						</button>
						<a href="{{ route('admin.fees.discounts.create') }}" class="btn btn-primary">
							<i data-feather="plus" class="me-1"></i> Add New
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-md-4">
						<form action="{{ route('admin.fees.discounts.index') }}" method="GET">
							<div class="input-group">
								<input type="text" name="search" class="form-control" placeholder="Search by name or code..." value="{{ request('search') }}">
								<button class="btn btn-outline-secondary" type="submit">
									<i data-feather="search"></i>
								</button>
							</div>
						</form>
					</div>
					<div class="col-md-3">
						<form action="{{ route('admin.fees.discounts.index') }}" method="GET">
							<select name="type" class="form-select" onchange="this.form.submit()">
								<option value="">All Types</option>
								<option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
								<option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
							</select>
						</form>
					</div>
					<div class="col-md-3">
						<form action="{{ route('admin.fees.discounts.index') }}" method="GET">
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
									<input type="checkbox" class="form-check-input" id="selectAll">
								</th>
								<th>#</th>
								<th>Code</th>
								<th>Name</th>
								<th>Type</th>
								<th>Amount</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($discounts as $discount)
								<tr>
									<td>
										<input type="checkbox" class="form-check-input item-checkbox" value="{{ $discount->id }}" data-name="{{ $discount->name }}">
									</td>
									<td>{{ $discounts->firstItem() + $loop->index }}</td>
									<td><span class="badge badge-light-primary">{{ $discount->code }}</span></td>
									<td><strong>{{ $discount->name }}</strong></td>
									<td>
										<span class="badge badge-light-{{ $discount->type === 'percentage' ? 'info' : 'warning' }}">
											{{ ucfirst($discount->type) }}
										</span>
									</td>
									<td>
										@if($discount->type === 'percentage')
											<strong>{{ number_format($discount->amount, 2) }}%</strong>
										@else
											<strong>₹{{ number_format($discount->amount, 2) }}</strong>
										@endif
									</td>
									<td>
										<span class="badge badge-light-{{ $discount->is_active ? 'success' : 'danger' }}">
											{{ $discount->is_active ? 'Active' : 'Inactive' }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.fees.discounts.edit', $discount) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.fees.discounts.destroy', $discount) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $discount->name }}">
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
											<i data-feather="percent" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No fee discounts found.</p>
											<a href="{{ route('admin.fees.discounts.create') }}" class="btn btn-primary mt-3">Add First Discount</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($discounts->hasPages())
					<div class="mt-3">
						{{ $discounts->links() }}
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
	function updateBulkState() {
		var checkedCount = jQuery('.item-checkbox:checked').length;
		var totalCount = jQuery('.item-checkbox').length;
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
		jQuery('.item-checkbox').prop('checked', jQuery(this).is(':checked'));
		updateBulkState();
	});

	jQuery(document).on('change', '.item-checkbox', function() {
		updateBulkState();
	});

	// Reset on load
	jQuery('#selectAll').prop('checked', false);
	jQuery('.item-checkbox').prop('checked', false);

	// Bulk delete
	jQuery(document).on('click', '#bulkDeleteBtn', function() {
		var ids = [];
		jQuery('.item-checkbox:checked').each(function() { ids.push(jQuery(this).val()); });

		if (ids.length === 0) return;

		Swal.fire({
			title: 'Delete Selected?',
			html: 'Delete <strong>' + ids.length + '</strong> discount(s)? This cannot be undone.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes, delete',
			reverseButtons: true
		}).then(function(result) {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ route("admin.fees.discounts.bulk-delete") }}',
					type: 'POST',
					data: { _token: '{{ csrf_token() }}', ids: ids },
					success: function(response) {
						Swal.fire('Deleted!', response.message, 'success').then(function() { window.location.reload(); });
					},
					error: function(xhr) {
						Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred.', 'error');
					}
				});
			}
		});
	});

	if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
