@extends('layouts.app')

@section('title', 'Vehicles')

@section('page-title', 'Transport - Vehicles')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.transport.vehicles.index') }}">Transport</a></li>
	<li class="breadcrumb-item active">Vehicles</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5>Vehicles List</h5>
				<a href="{{ route('admin.transport.vehicles.create') }}" class="btn btn-primary btn-sm">
					<i data-feather="plus" class="me-1"></i> Add Vehicle
				</a>
			</div>
			<div class="card-body">
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

				<div class="row mb-3">
					<div class="col-md-4">
						<form action="{{ route('admin.transport.vehicles.index') }}" method="GET">
							<div class="input-group">
								<select name="status" class="form-select">
									<option value="">All Status</option>
									<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
									<option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
									<option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
								</select>
								<button type="submit" class="btn btn-primary">Filter</button>
							</div>
						</form>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Vehicle No</th>
								<th>Model</th>
								<th>Registration No</th>
								<th>Driver</th>
								<th>Capacity</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($vehicles as $vehicle)
								<tr>
									<td>{{ $vehicle->vehicle_no }}</td>
									<td>{{ $vehicle->vehicle_model }}</td>
									<td>{{ $vehicle->registration_no }}</td>
									<td>
										@if($vehicle->driver_name)
											{{ $vehicle->driver_name }}<br>
											<small class="text-muted">{{ $vehicle->driver_contact }}</small>
										@else
											<span class="text-muted">-</span>
										@endif
									</td>
									<td>{{ $vehicle->max_seating_capacity }} seats</td>
									<td>
										@if($vehicle->status == 'active')
											<span class="badge bg-success">Active</span>
										@elseif($vehicle->status == 'maintenance')
											<span class="badge bg-warning">Maintenance</span>
										@else
											<span class="badge bg-secondary">Inactive</span>
										@endif
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.transport.vehicles.edit', $vehicle) }}" title="Edit">
												<svg>
													<use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use>
												</svg>
											</a>
											<form action="{{ route('admin.transport.vehicles.destroy', $vehicle) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $vehicle->vehicle_no }}">
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
									<td colspan="7" class="text-center text-muted py-4">No vehicles found.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<div class="mt-3">
					{{ $vehicles->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
