@extends('layouts.app')

@section('title', 'Vehicles')

@section('page-title', 'Transport - Vehicles')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item">Transport</li>
	<li class="breadcrumb-item active">Vehicles</li>
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

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Vehicles</h5>
					<a href="{{ route('admin.transport.vehicles.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Add Vehicle
					</a>
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Vehicle No</th>
								<th>Model</th>
								<th>Registration</th>
								<th>Capacity</th>
								<th>Driver</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($vehicles as $vehicle)
								<tr>
									<td>{{ $vehicles->firstItem() + $loop->index }}</td>
									<td><strong>{{ $vehicle->vehicle_no }}</strong></td>
									<td>{{ $vehicle->vehicle_model }}</td>
									<td>{{ $vehicle->registration_no }}</td>
									<td>{{ $vehicle->max_seating_capacity }}</td>
									<td>{{ $vehicle->driver_name ?? '-' }}</td>
									<td>
										<span class="badge badge-light-{{ $vehicle->status === 'active' ? 'success' : 'warning' }}">
											{{ ucfirst($vehicle->status) }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.transport.vehicles.edit', $vehicle) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.transport.vehicles.destroy', $vehicle) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $vehicle->vehicle_no }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center py-4">
										<p class="text-muted">No vehicles found.</p>
										<a href="{{ route('admin.transport.vehicles.create') }}" class="btn btn-primary">Add First Vehicle</a>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($vehicles->hasPages())
					<div class="mt-3">{{ $vehicles->links() }}</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
