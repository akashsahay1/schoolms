@extends('layouts.app')

@section('title', 'Add Fee Structure')

@section('page-title', 'Add Fee Structure')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.structure') }}">Fee Structure</a></li>
	<li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-10 col-lg-8">
		<div class="card">
			<div class="card-header">
				<h5>Fee Structure Information</h5>
			</div>
			<div class="card-body">
				<form action="{{ route('admin.fees.structure.store') }}" method="POST">
					@csrf

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
							<select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
								<option value="">Select Academic Year</option>
								@foreach($academicYears as $year)
									<option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
								@endforeach
							</select>
							@error('academic_year_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
							<select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
								<option value="">Select Class</option>
								@foreach($classes as $class)
									<option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
								@endforeach
							</select>
							@error('class_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="fee_type_id" class="form-label">Fee Type <span class="text-danger">*</span></label>
							<select class="form-select @error('fee_type_id') is-invalid @enderror" id="fee_type_id" name="fee_type_id" required>
								<option value="">Select Fee Type</option>
								@foreach($feeTypes as $type)
									<option value="{{ $type->id }}" {{ old('fee_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
								@endforeach
							</select>
							@error('fee_type_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="fee_group_id" class="form-label">Fee Group <span class="text-danger">*</span></label>
							<select class="form-select @error('fee_group_id') is-invalid @enderror" id="fee_group_id" name="fee_group_id" required>
								<option value="">Select Fee Group</option>
								@foreach($feeGroups as $group)
									<option value="{{ $group->id }}" {{ old('fee_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
								@endforeach
							</select>
							@error('fee_group_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="amount" class="form-label">Amount (₹) <span class="text-danger">*</span></label>
							<input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required placeholder="0.00">
							@error('amount')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="due_date" class="form-label">Due Date</label>
							<input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
							@error('due_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="fine_amount" class="form-label">Fine Amount (₹)</label>
							<input type="number" step="0.01" class="form-control @error('fine_amount') is-invalid @enderror" id="fine_amount" name="fine_amount" value="{{ old('fine_amount', 0) }}" placeholder="0.00">
							@error('fine_amount')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="fine_type" class="form-label">Fine Type</label>
							<select class="form-select @error('fine_type') is-invalid @enderror" id="fine_type" name="fine_type">
								<option value="fixed" {{ old('fine_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
								<option value="percentage" {{ old('fine_type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
							</select>
							@error('fine_type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Description</label>
						<textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Optional description">{{ old('description') }}</textarea>
						@error('description')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
							<label class="form-check-label" for="is_active">Active</label>
						</div>
					</div>

					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.fees.structure') }}" class="btn btn-light">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Save Fee Structure
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
