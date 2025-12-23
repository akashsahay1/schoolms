@extends('layouts.app')

@section('title', 'Duplicate Fee Structure')

@section('page-title', 'Duplicate Fee Structure')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.structure') }}">Fee Structure</a></li>
	<li class="breadcrumb-item active">Duplicate</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-8 col-lg-6">
		<div class="card">
			<div class="card-header">
				<h5>Duplicate Fee Structure</h5>
			</div>
			<div class="card-body">
				<div class="alert alert-info">
					<strong>Original Structure:</strong><br>
					<strong>Academic Year:</strong> {{ $feeStructure->academicYear->name }}<br>
					<strong>Class:</strong> {{ $feeStructure->schoolClass->name }}<br>
					<strong>Fee Type:</strong> {{ $feeStructure->feeType->name }}<br>
					<strong>Amount:</strong> ₹{{ number_format($feeStructure->amount, 2) }}
				</div>

				<form action="{{ route('admin.fees.structure.duplicate', $feeStructure) }}" method="POST">
					@csrf

					<div class="mb-3">
						<label for="academic_year_id" class="form-label">Target Academic Year <span class="text-danger">*</span></label>
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

					<div class="mb-3">
						<label for="class_id" class="form-label">Target Class <span class="text-danger">*</span></label>
						<select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
							<option value="">Select Class</option>
							@foreach($classes as $class)
								<option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
							@endforeach
						</select>
						@error('class_id')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<small class="text-muted">The fee type, amount, and other details will remain the same</small>
					</div>

					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.fees.structure') }}" class="btn btn-light">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="copy" class="me-1"></i> Duplicate Structure
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
