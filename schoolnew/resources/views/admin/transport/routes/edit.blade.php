@extends('layouts.app')

@section('title', 'Edit Route')

@section('page-title', 'Transport - Edit Route')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.transport.routes.index') }}">Routes</a></li>
	<li class="breadcrumb-item active">Edit Route</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-10 col-lg-8">
		<div class="card">
			<div class="card-header">
				<h5>Edit Route Information</h5>
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

				<form action="{{ route('admin.transport.routes.update', $route) }}" method="POST">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="route_name" class="form-label">Route Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('route_name') is-invalid @enderror" id="route_name" name="route_name" value="{{ old('route_name', $route->route_name) }}" placeholder="e.g., Route A - City Center" required>
							@error('route_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="vehicle_id" class="form-label">Vehicle <span class="text-danger">*</span></label>
							<select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id" required>
								<option value="">Select Vehicle</option>
								@foreach($vehicles as $vehicle)
									<option value="{{ $vehicle->id }}" {{ old('vehicle_id', $route->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
										{{ $vehicle->vehicle_no }} - {{ $vehicle->vehicle_model }}
									</option>
								@endforeach
							</select>
							@error('vehicle_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="start_place" class="form-label">Start Place <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('start_place') is-invalid @enderror" id="start_place" name="start_place" value="{{ old('start_place', $route->start_place) }}" placeholder="e.g., Main Gate" required>
							@error('start_place')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="end_place" class="form-label">End Place <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('end_place') is-invalid @enderror" id="end_place" name="end_place" value="{{ old('end_place', $route->end_place) }}" placeholder="e.g., City Center" required>
							@error('end_place')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label for="fare_amount" class="form-label">Fare Amount (₹) <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('fare_amount') is-invalid @enderror" id="fare_amount" name="fare_amount" value="{{ old('fare_amount', $route->fare_amount) }}" step="0.01" min="0" placeholder="0.00" required>
							@error('fare_amount')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-4 mb-3">
							<label for="start_time" class="form-label">Start Time</label>
							<input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $route->start_time ? \Carbon\Carbon::parse($route->start_time)->format('H:i') : '') }}">
							@error('start_time')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-4 mb-3">
							<label for="end_time" class="form-label">End Time</label>
							<input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $route->end_time ? \Carbon\Carbon::parse($route->end_time)->format('H:i') : '') }}">
							@error('end_time')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="mb-3">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $route->is_active) ? 'checked' : '' }}>
							<label class="form-check-label" for="is_active">Active</label>
						</div>
					</div>

					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.transport.routes.index') }}" class="btn btn-light">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Update Route
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
