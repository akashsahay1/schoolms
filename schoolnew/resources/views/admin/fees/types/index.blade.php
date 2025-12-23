@extends('layouts.app')

@section('title', 'Fee Types')

@section('page-title', 'Fee Types')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="#">Fees</a></li>
	<li class="breadcrumb-item active">Fee Types</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Fee Types</h5>
					<a href="{{ route('admin.fees.types.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Add Fee Type
					</a>
				</div>
			</div>
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-md-4">
						<form action="{{ route('admin.fees.types.index') }}" method="GET">
							<div class="input-group">
								<input type="text" name="search" class="form-control" placeholder="Search by name or code..." value="{{ request('search') }}">
								<button class="btn btn-outline-secondary" type="submit">
									<i data-feather="search"></i>
								</button>
							</div>
						</form>
					</div>
					<div class="col-md-3">
						<form action="{{ route('admin.fees.types.index') }}" method="GET">
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
								<th>#</th>
								<th>Code</th>
								<th>Name</th>
								<th>Description</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($feeTypes as $feeType)
								<tr>
									<td>{{ $feeTypes->firstItem() + $loop->index }}</td>
									<td><span class="badge badge-light-primary">{{ $feeType->code }}</span></td>
									<td><strong>{{ $feeType->name }}</strong></td>
									<td>{{ Str::limit($feeType->description, 50) ?? '-' }}</td>
									<td>
										<span class="badge badge-light-{{ $feeType->is_active ? 'success' : 'danger' }}">
											{{ $feeType->is_active ? 'Active' : 'Inactive' }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.fees.types.edit', $feeType) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.fees.types.destroy', $feeType) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $feeType->name }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="dollar-sign" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No fee types found.</p>
											<a href="{{ route('admin.fees.types.create') }}" class="btn btn-primary mt-3">Add First Fee Type</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($feeTypes->hasPages())
					<div class="mt-3">
						{{ $feeTypes->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
