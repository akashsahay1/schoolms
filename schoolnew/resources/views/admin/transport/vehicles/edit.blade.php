@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('page-title', 'Transport - Edit Vehicle')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.transport.vehicles.index') }}">Vehicles</a></li>
	<li class="breadcrumb-item active">Edit Vehicle</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-10 col-lg-8">
		<div class="card">
			<div class="card-header">
				<h5>Edit Vehicle Information</h5>
			</div>
			<div class="card-body">
				@if(session('error'))
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						{{ session('error') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				@if($errors->any())
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Please correct the following errors:</strong>
						<ul class="mb-0 mt-2">
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				<form action="{{ route('admin.transport.vehicles.update', $vehicle) }}" method="POST">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="vehicle_no" class="form-label">Vehicle Number <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('vehicle_no') is-invalid @enderror" id="vehicle_no" name="vehicle_no" value="{{ old('vehicle_no', $vehicle->vehicle_no) }}" required>
							@error('vehicle_no')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="registration_no" class="form-label">Registration Number</label>
							<input type="text" class="form-control @error('registration_no') is-invalid @enderror" id="registration_no" name="registration_no" value="{{ old('registration_no', $vehicle->registration_no) }}" readonly>
							<small class="text-muted">Registration number cannot be changed</small>
							@error('registration_no')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row">
						<div class="col-md-8 mb-3">
							<label for="vehicle_model" class="form-label">Vehicle Model <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('vehicle_model') is-invalid @enderror" id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model', $vehicle->vehicle_model) }}" required>
							@error('vehicle_model')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-4 mb-3">
							<label for="year_made" class="form-label">Year Made</label>
							<input type="number" class="form-control" id="year_made" name="year_made" value="{{ old('year_made', $vehicle->year_made) }}" readonly>
							<small class="text-muted">Year cannot be changed</small>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="chasis_no" class="form-label">Chasis Number</label>
							<input type="text" class="form-control" id="chasis_no" name="chasis_no" value="{{ old('chasis_no', $vehicle->chasis_no) }}" readonly>
							<small class="text-muted">Chasis number cannot be changed</small>
						</div>

						<div class="col-md-6 mb-3">
							<label for="max_seating_capacity" class="form-label">Seating Capacity <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('max_seating_capacity') is-invalid @enderror" id="max_seating_capacity" name="max_seating_capacity" value="{{ old('max_seating_capacity', $vehicle->max_seating_capacity) }}" min="1" required>
							@error('max_seating_capacity')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<hr class="my-4">
					<h6 class="mb-3">Driver Details</h6>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="driver_name" class="form-label">Driver Name</label>
							<input type="text" class="form-control @error('driver_name') is-invalid @enderror" id="driver_name" name="driver_name" value="{{ old('driver_name', $vehicle->driver_name) }}">
							@error('driver_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="driver_contact" class="form-label">Driver Contact</label>
							<input type="text" class="form-control @error('driver_contact') is-invalid @enderror" id="driver_contact" name="driver_contact" value="{{ old('driver_contact', $vehicle->driver_contact) }}">
							@error('driver_contact')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="mb-3">
						<label for="driver_license" class="form-label">Driver License Number</label>
						<input type="text" class="form-control" id="driver_license" name="driver_license" value="{{ old('driver_license', $vehicle->driver_license) }}" readonly>
						<small class="text-muted">License number cannot be changed</small>
					</div>

					<hr class="my-4">

					<div class="mb-3">
						<label for="status" class="form-label">Status <span class="text-danger">*</span></label>
						<select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
							<option value="">Select Status</option>
							<option value="active" {{ old('status', $vehicle->status) == 'active' ? 'selected' : '' }}>Active</option>
							<option value="maintenance" {{ old('status', $vehicle->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
							<option value="inactive" {{ old('status', $vehicle->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
						</select>
						@error('status')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label for="note" class="form-label">Note</label>
						<textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3">{{ old('note', $vehicle->note) }}</textarea>
						@error('note')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.transport.vehicles.index') }}" class="btn btn-light">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Update Vehicle
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
