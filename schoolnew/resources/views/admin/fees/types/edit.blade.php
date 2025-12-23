@extends('layouts.app')

@section('title', 'Edit Fee Type')

@section('page-title', 'Edit Fee Type')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.types.index') }}">Fee Types</a></li>
	<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-8 col-lg-6">
		<div class="card">
			<div class="card-header">
				<h5>Edit Fee Type</h5>
			</div>
			<div class="card-body">
				<form action="{{ route('admin.fees.types.update', $feeType) }}" method="POST">
					@csrf
					@method('PUT')

					<div class="mb-3">
						<label for="code" class="form-label">Fee Code <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $feeType->code) }}" required>
						@error('code')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<small class="text-muted">Unique code for this fee type</small>
					</div>

					<div class="mb-3">
						<label for="name" class="form-label">Fee Type Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $feeType->name) }}" required>
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Description</label>
						<textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $feeType->description) }}</textarea>
						@error('description')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $feeType->is_active) ? 'checked' : '' }}>
							<label class="form-check-label" for="is_active">Active</label>
						</div>
					</div>

					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.fees.types.index') }}" class="btn btn-light">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Update Fee Type
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
