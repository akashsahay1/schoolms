@extends('layouts.app')

@section('title', 'View Message')

@section('page-title', 'Contact Message Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.messaging.contact-messages.index') }}">Contact Messages</a></li>
	<li class="breadcrumb-item active">{{ Str::limit($contactMessage->subject, 30) }}</li>
@endsection

@section('content')
<div class="row">
	<!-- Message Details -->
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0">{{ $contactMessage->subject }}</h5>
				<div class="d-flex gap-2">
					<span class="badge {{ $contactMessage->getPriorityBadgeClass() }}">{{ $contactMessage->getPriorityLabel() }}</span>
					<span class="badge {{ $contactMessage->getStatusBadgeClass() }}">{{ $contactMessage->getStatusLabel() }}</span>
				</div>
			</div>
			<div class="card-body">
				@if(session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ session('success') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				<!-- Original Message -->
				<div class="border rounded p-3 mb-4">
					<div class="d-flex justify-content-between align-items-start mb-3">
						<div class="d-flex align-items-center gap-2">
							<div class="bg-primary d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; font-size: 16px;">
								{{ strtoupper(substr($contactMessage->user->name ?? 'U', 0, 1)) }}
							</div>
							<div>
								<strong>{{ $contactMessage->user->name ?? 'Unknown' }}</strong>
								<br><small class="text-muted">{{ $contactMessage->user->email ?? '' }}</small>
							</div>
						</div>
						<small class="text-muted">{{ $contactMessage->created_at->format('M d, Y h:i A') }}</small>
					</div>
					<div class="ps-5">
						<p class="mb-0" style="white-space: pre-wrap;">{{ $contactMessage->message }}</p>
					</div>
				</div>

				<!-- Admin Response (if exists) -->
				@if($contactMessage->admin_response)
					<div class="border border-success rounded p-3 mb-4" style="background-color: #f0fff4;">
						<div class="d-flex justify-content-between align-items-start mb-3">
							<div class="d-flex align-items-center gap-2">
								<div class="bg-success d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; font-size: 16px;">
									<i class="fa-solid fa-shield-halved"></i>
								</div>
								<div>
									<strong>{{ $contactMessage->respondedBy->name ?? 'Admin' }}</strong>
									<br><small class="text-muted">Admin Response</small>
								</div>
							</div>
							<small class="text-muted">{{ $contactMessage->responded_at?->format('M d, Y h:i A') }}</small>
						</div>
						<div class="ps-5">
							<p class="mb-0" style="white-space: pre-wrap;">{{ $contactMessage->admin_response }}</p>
						</div>
					</div>
				@endif

				<!-- Response Form -->
				<div class="border-top pt-4">
					<h6 class="mb-3"><i data-feather="send" style="width: 16px; height: 16px;"></i> {{ $contactMessage->admin_response ? 'Update Response' : 'Send Response' }}</h6>
					<form action="{{ route('admin.messaging.contact-messages.respond', $contactMessage) }}" method="POST">
						@csrf
						<div class="mb-3">
							<textarea name="admin_response" class="form-control @error('admin_response') is-invalid @enderror" rows="5" placeholder="Type your response here..." required>{{ old('admin_response', $contactMessage->admin_response) }}</textarea>
							@error('admin_response')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="row align-items-center">
							<div class="col-md-4 mb-3">
								<label class="form-label">Update Status</label>
								<select name="status" class="form-select">
									<option value="in_progress" {{ $contactMessage->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
									<option value="resolved" {{ $contactMessage->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
									<option value="closed" {{ $contactMessage->status === 'closed' ? 'selected' : '' }}>Closed</option>
								</select>
							</div>
							<div class="col-md-8 mb-3 text-end">
								<button type="submit" class="btn btn-primary">
									<i data-feather="send" style="width: 14px; height: 14px;" class="me-1"></i> Send Response
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Sidebar Info -->
	<div class="col-lg-4">
		<!-- Sender Info -->
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Sender Details</h6>
			</div>
			<div class="card-body p-0">
				<ul class="list-group list-group-flush">
					<li class="list-group-item d-flex justify-content-between">
						<span class="text-muted">Name</span>
						<strong>{{ $contactMessage->user->name ?? 'N/A' }}</strong>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span class="text-muted">Email</span>
						<span style="word-break: break-all;">{{ $contactMessage->user->email ?? 'N/A' }}</span>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span class="text-muted">Role</span>
						<span>{{ $contactMessage->user->roles->first()->name ?? 'N/A' }}</span>
					</li>
					@if($contactMessage->student)
						<li class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Student</span>
							<a href="{{ route('admin.students.show', $contactMessage->student) }}">{{ $contactMessage->student->full_name }}</a>
						</li>
						<li class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Class</span>
							<span>{{ $contactMessage->student->schoolClass->name ?? 'N/A' }}</span>
						</li>
					@endif
				</ul>
			</div>
		</div>

		<!-- Message Info -->
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Message Info</h6>
			</div>
			<div class="card-body p-0">
				<ul class="list-group list-group-flush">
					<li class="list-group-item d-flex justify-content-between">
						<span class="text-muted">Category</span>
						<span class="badge badge-light-primary">{{ $contactMessage->getCategoryLabel() }}</span>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span class="text-muted">Priority</span>
						<span class="badge {{ $contactMessage->getPriorityBadgeClass() }}">{{ $contactMessage->getPriorityLabel() }}</span>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span class="text-muted">Status</span>
						<span class="badge {{ $contactMessage->getStatusBadgeClass() }}">{{ $contactMessage->getStatusLabel() }}</span>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span class="text-muted">Submitted</span>
						<span>{{ $contactMessage->created_at->format('M d, Y') }}</span>
					</li>
					@if($contactMessage->assignedTo)
						<li class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Assigned To</span>
							<span>{{ $contactMessage->assignedTo->name }}</span>
						</li>
					@endif
				</ul>
			</div>
		</div>

		<!-- Quick Status Update -->
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Quick Status Update</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('admin.messaging.contact-messages.update-status', $contactMessage) }}" method="POST">
					@csrf
					@method('PATCH')
					<div class="d-flex gap-2">
						<select name="status" class="form-select form-select-sm">
							@foreach(\App\Models\ContactMessage::STATUSES as $key => $label)
								<option value="{{ $key }}" {{ $contactMessage->status === $key ? 'selected' : '' }}>{{ $label }}</option>
							@endforeach
						</select>
						<button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Actions -->
		<div class="card">
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="{{ route('admin.messaging.contact-messages.index') }}" class="btn btn-outline-secondary">
						<i data-feather="arrow-left" class="me-1"></i> Back to Messages
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
