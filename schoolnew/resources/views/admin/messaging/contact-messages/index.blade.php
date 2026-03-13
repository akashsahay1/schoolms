@extends('layouts.app')

@section('title', 'Contact Messages')

@section('page-title', 'Portal Contact Messages')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Contact Messages</li>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row">
	<div class="col-sm-6 col-xl-3">
		<div class="card">
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between">
					<div>
						<p class="text-muted mb-1">Open</p>
						<h4 class="mb-0">{{ $stats['open'] }}</h4>
					</div>
					<div class="bg-warning d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px;">
						<i class="fa-solid fa-envelope-open text-white" style="font-size: 22px;"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="card">
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between">
					<div>
						<p class="text-muted mb-1">In Progress</p>
						<h4 class="mb-0">{{ $stats['in_progress'] }}</h4>
					</div>
					<div class="bg-info d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px;">
						<i class="fa-solid fa-spinner text-white" style="font-size: 22px;"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="card">
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between">
					<div>
						<p class="text-muted mb-1">Resolved</p>
						<h4 class="mb-0">{{ $stats['resolved'] }}</h4>
					</div>
					<div class="bg-success d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px;">
						<i class="fa-solid fa-circle-check text-white" style="font-size: 22px;"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="card">
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between">
					<div>
						<p class="text-muted mb-1">Total</p>
						<h4 class="mb-0">{{ $stats['total'] }}</h4>
					</div>
					<div class="bg-primary d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px;">
						<i class="fa-solid fa-envelope text-white" style="font-size: 22px;"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h5>Contact Messages from Students & Parents</h5>
			</div>
			<div class="card-body">
				@if(session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ session('success') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				<!-- Filters -->
				<form action="{{ route('admin.messaging.contact-messages.index') }}" method="GET" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<input type="text" name="search" class="form-control" placeholder="Search by name, subject..." value="{{ request('search') }}">
						</div>
						<div class="col-md-2">
							<select name="status" class="form-select">
								<option value="">All Status</option>
								@foreach(\App\Models\ContactMessage::STATUSES as $key => $label)
									<option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<select name="category" class="form-select">
								<option value="">All Categories</option>
								@foreach(\App\Models\ContactMessage::CATEGORIES as $key => $label)
									<option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<select name="priority" class="form-select">
								<option value="">All Priorities</option>
								@foreach(\App\Models\ContactMessage::PRIORITIES as $key => $label)
									<option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<button type="submit" class="btn btn-primary">Filter</button>
							<a href="{{ route('admin.messaging.contact-messages.index') }}" class="btn btn-outline-secondary">Reset</a>
						</div>
					</div>
				</form>

				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>From</th>
								<th>Subject</th>
								<th>Category</th>
								<th>Priority</th>
								<th>Status</th>
								<th>Date</th>
								<th style="width: 120px;">Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($messages as $msg)
								<tr class="{{ $msg->status === 'open' ? 'table-light fw-bold' : '' }}">
									<td>{{ $messages->firstItem() + $loop->index }}</td>
									<td>
										<div>{{ $msg->user->name ?? 'Unknown' }}</div>
										<small class="text-muted">{{ $msg->user->email ?? '' }}</small>
									</td>
									<td>{{ Str::limit($msg->subject, 40) }}</td>
									<td><span class="badge badge-light-primary">{{ $msg->getCategoryLabel() }}</span></td>
									<td><span class="badge {{ $msg->getPriorityBadgeClass() }}">{{ $msg->getPriorityLabel() }}</span></td>
									<td><span class="badge {{ $msg->getStatusBadgeClass() }}">{{ $msg->getStatusLabel() }}</span></td>
									<td>{{ $msg->created_at->format('M d, Y h:i A') }}</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.messaging.contact-messages.show', $msg) }}" title="View">
												<svg>
													<use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use>
												</svg>
											</a>
											<form action="{{ route('admin.messaging.contact-messages.destroy', $msg) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $msg->subject }}">
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
									<td colspan="8" class="text-center py-4">
										<p class="text-muted mb-0">No contact messages found.</p>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($messages->hasPages())
					<div class="d-flex justify-content-center mt-4">
						{{ $messages->withQueryString()->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
