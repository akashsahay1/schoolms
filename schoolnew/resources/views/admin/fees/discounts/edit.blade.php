@extends('layouts.app')

@section('title', 'Edit Fee Discount')

@section('page-title', 'Edit Fee Discount')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.discounts.index') }}">Fee Discounts</a></li>
	<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-8 col-lg-6">
		<div class="card">
			<div class="card-header">
				<h5>Edit Discount Information</h5>
			</div>
			<div class="card-body">
				@if(session('error'))
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						{{ session('error') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				<form action="{{ route('admin.fees.discounts.update', $discount) }}" method="POST">
					@csrf
					@method('PUT')

					<div class="mb-3">
						<label for="code" class="form-label">Discount Code <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $discount->code) }}" required placeholder="e.g., SIBLING, STAFF, MERIT">
						@error('code')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<small class="text-muted">Unique code for this discount</small>
					</div>

					<div class="mb-3">
						<label for="name" class="form-label">Discount Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $discount->name) }}" required placeholder="e.g., Sibling Discount, Staff Ward Discount">
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="type" class="form-label">Discount Type <span class="text-danger">*</span></label>
							<select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
								<option value="">Select Type</option>
								<option value="percentage" {{ old('type', $discount->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
								<option value="fixed" {{ old('type', $discount->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
							</select>
							@error('type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text" id="amount-prefix">{{ $discount->type === 'percentage' ? '%' : '₹' }}</span>
								<input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $discount->amount) }}" step="0.01" min="0" required placeholder="0.00">
								@error('amount')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
							<small class="text-muted" id="amount-hint">
								{{ $discount->type === 'percentage' ? 'Enter percentage value (0-100)' : 'Enter fixed discount amount' }}
							</small>
						</div>
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Description</label>
						<textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Optional description about this discount">{{ old('description', $discount->description) }}</textarea>
						@error('description')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
							<label class="form-check-label" for="is_active">Active</label>
						</div>
					</div>

					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.fees.discounts.index') }}" class="btn btn-light">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Update Discount
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery('#type').on('change', function() {
		var type = jQuery(this).val();
		if (type === 'percentage') {
			jQuery('#amount-prefix').text('%');
			jQuery('#amount-hint').text('Enter percentage value (0-100)');
			jQuery('#amount').attr('max', 100);
		} else if (type === 'fixed') {
			jQuery('#amount-prefix').text('₹');
			jQuery('#amount-hint').text('Enter fixed discount amount');
			jQuery('#amount').removeAttr('max');
		} else {
			jQuery('#amount-prefix').text('₹/%');
			jQuery('#amount-hint').text('Enter percentage (0-100) or fixed amount');
			jQuery('#amount').removeAttr('max');
		}
	});
});
</script>
@endpush
