@extends('layouts.app')

@section('title', 'Notices')
@section('page-title', 'Notices')

@section('breadcrumb')
	<li class="breadcrumb-item">Communication</li>
	<li class="breadcrumb-item active">Notices</li>
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

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Notices</h5>
					<a href="{{ route('admin.notices.create') }}" class="btn btn-primary">
						<i class="fa fa-plus me-1"></i> Add Notice
					</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.notices.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<select name="type" class="form-select">
								<option value="">All Types</option>
								@foreach(\App\Models\Notice::TYPES as $key => $label)
									<option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<select name="status" class="form-select">
								<option value="">All Status</option>
								<option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
								<option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
								<option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Filter
							</button>
						</div>
						@if(request()->hasAny(['type', 'status']))
							<div class="col-md-1">
								<a href="{{ route('admin.notices.index') }}" class="btn btn-outline-secondary w-100" title="Clear Filters">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Notices Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Title</th>
								<th>Type</th>
								<th>Publish Date</th>
								<th>Expiry</th>
								<th>Status</th>
								<th>Created By</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($notices as $notice)
								<tr>
									<td>{{ Str::limit($notice->title, 40) }}</td>
									<td><span class="badge {{ $notice->getTypeBadgeClass() }}">{{ $notice->getTypeLabel() }}</span></td>
									<td>{{ $notice->publish_date->format('M d, Y') }}</td>
									<td>{{ $notice->expiry_date ? $notice->expiry_date->format('M d, Y') : 'No Expiry' }}</td>
									<td>
										@if(!$notice->is_published)
											<span class="badge badge-light-secondary">Draft</span>
										@elseif($notice->isExpired())
											<span class="badge badge-light-danger">Expired</span>
										@else
											<span class="badge badge-light-success">Published</span>
										@endif
									</td>
									<td>{{ $notice->creator->name ?? 'N/A' }}</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.notices.show', $notice) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.notices.edit', $notice) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $notice->title }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="bell" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No notices found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
				@if($notices->hasPages())
					<div class="mt-3">
						{{ $notices->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
