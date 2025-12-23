@extends('layouts.app')

@section('title', 'Transport Routes')

@section('page-title', 'Transport - Routes')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item">Transport</li>
	<li class="breadcrumb-item active">Routes</li>
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
					<h5>All Routes</h5>
					<a href="{{ route('admin.transport.routes.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Add Route
					</a>
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Route Name</th>
								<th>Vehicle</th>
								<th>Start - End</th>
								<th>Fare</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($routes as $route)
								<tr>
									<td>{{ $routes->firstItem() + $loop->index }}</td>
									<td><strong>{{ $route->route_name }}</strong></td>
									<td><span class="badge badge-light-info">{{ $route->vehicle->vehicle_no ?? '-' }}</span></td>
									<td>{{ $route->start_place }} - {{ $route->end_place }}</td>
									<td>₹{{ number_format($route->fare_amount, 2) }}</td>
									<td>
										<span class="badge badge-light-{{ $route->is_active ? 'success' : 'danger' }}">
											{{ $route->is_active ? 'Active' : 'Inactive' }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.transport.routes.edit', $route) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.transport.routes.destroy', $route) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $route->route_name }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="text-center py-4">
										<p class="text-muted">No routes found.</p>
										<a href="{{ route('admin.transport.routes.create') }}" class="btn btn-primary">Add First Route</a>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($routes->hasPages())
					<div class="mt-3">{{ $routes->links() }}</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
