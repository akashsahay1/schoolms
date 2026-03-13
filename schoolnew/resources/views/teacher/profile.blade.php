@extends('layouts.teacher-portal')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('breadcrumb')
<li class="breadcrumb-item active">My Profile</li>
@endsection

@section('content')
<div class="row">
	<!-- Profile Card -->
	<div class="col-xl-4 mb-4">
		<div class="card">
			<div class="card-body text-center">
				<div class="mb-4">
					@if($staff->photo)
						<img src="{{ asset('storage/' . $staff->photo) }}" alt="{{ $staff->full_name }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
					@else
						<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 48px;">
							{{ strtoupper(substr($staff->first_name, 0, 1)) }}{{ strtoupper(substr($staff->last_name ?? '', 0, 1)) }}
						</div>
					@endif
				</div>
				<h4 class="mb-1">{{ $staff->full_name }}</h4>
				<p class="text-muted mb-2">{{ $staff->designation->name ?? 'Staff' }}</p>
				@if($staff->department)
					<span class="badge bg-primary">{{ $staff->department->name }}</span>
				@endif
				<hr>
				<div class="text-start">
					<p class="mb-2" style="word-break: break-all;">
						<i data-feather="mail" class="me-2 text-primary" style="width: 16px; height: 16px;"></i>
						{{ $user->email }}
					</p>
					@if($staff->phone)
						<p class="mb-2">
							<i data-feather="phone" class="me-2 text-primary" style="width: 16px; height: 16px;"></i>
							{{ $staff->phone }}
						</p>
					@endif
					@if($staff->employee_id)
						<p class="mb-0">
							<i data-feather="credit-card" class="me-2 text-primary" style="width: 16px; height: 16px;"></i>
							Employee ID: {{ $staff->employee_id }}
						</p>
					@endif
				</div>
			</div>
		</div>
	</div>

	<!-- Profile Details -->
	<div class="col-xl-8 mb-4">
		<div class="card">
			<div class="card-header pb-0 border-0">
				<h5 class="mb-0">
					<i data-feather="info" style="width: 18px; height: 18px;" class="me-2"></i>Profile Information
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-4">
					<!-- Personal Information -->
					<div class="col-12">
						<h6 class="text-primary mb-3">
							<i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>
							Personal Information
						</h6>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">First Name</label>
						<p class="mb-0 fw-medium">{{ $staff->first_name }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Last Name</label>
						<p class="mb-0 fw-medium">{{ $staff->last_name ?? '-' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Gender</label>
						<p class="mb-0 fw-medium">{{ ucfirst($staff->gender ?? '-') }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Date of Birth</label>
						<p class="mb-0 fw-medium">{{ $staff->dob ? \Carbon\Carbon::parse($staff->dob)->format('M d, Y') : '-' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Blood Group</label>
						<p class="mb-0 fw-medium">{{ $staff->blood_group ?? '-' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Marital Status</label>
						<p class="mb-0 fw-medium">{{ ucfirst($staff->marital_status ?? '-') }}</p>
					</div>

					<!-- Contact Information -->
					<div class="col-12 mt-4">
						<h6 class="text-primary mb-3">
							<i data-feather="phone" class="me-2" style="width: 16px; height: 16px;"></i>
							Contact Information
						</h6>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Email</label>
						<p class="mb-0 fw-medium" style="word-break: break-all;">{{ $staff->email ?? $user->email }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Phone</label>
						<p class="mb-0 fw-medium">{{ $staff->phone ?? '-' }}</p>
					</div>
					<div class="col-12">
						<label class="text-muted small">Address</label>
						<p class="mb-0 fw-medium">
							@if($staff->current_address)
								{{ $staff->current_address }}
							@else
								-
							@endif
						</p>
					</div>

					<!-- Employment Information -->
					<div class="col-12 mt-4">
						<h6 class="text-primary mb-3">
							<i data-feather="briefcase" class="me-2" style="width: 16px; height: 16px;"></i>
							Employment Information
						</h6>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Employee ID</label>
						<p class="mb-0 fw-medium">{{ $staff->employee_id ?? '-' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Designation</label>
						<p class="mb-0 fw-medium">{{ $staff->designation->name ?? '-' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Department</label>
						<p class="mb-0 fw-medium">{{ $staff->department->name ?? '-' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Joining Date</label>
						<p class="mb-0 fw-medium">{{ $staff->joining_date ? \Carbon\Carbon::parse($staff->joining_date)->format('M d, Y') : '-' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Employment Type</label>
						<p class="mb-0 fw-medium">{{ ucfirst($staff->contract_type ?? '-') }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Status</label>
						<p class="mb-0">
							@if($staff->status == 'active')
								<span class="badge bg-success">Active</span>
							@else
								<span class="badge bg-danger">{{ ucfirst($staff->status) }}</span>
							@endif
						</p>
					</div>

					<!-- Qualifications -->
					@if($staff->qualification)
					<div class="col-12 mt-4">
						<h6 class="text-primary mb-3">
							<i data-feather="award" class="me-2" style="width: 16px; height: 16px;"></i>
							Qualifications
						</h6>
					</div>
					<div class="col-12">
						<p class="mb-0 fw-medium">{{ $staff->qualification }}</p>
					</div>
					@endif

					<!-- Experience -->
					@if($staff->experience)
					<div class="col-12 mt-4">
						<h6 class="text-primary mb-3">
							<i data-feather="clock" class="me-2" style="width: 16px; height: 16px;"></i>
							Experience
						</h6>
					</div>
					<div class="col-12">
						<p class="mb-0 fw-medium">{{ $staff->experience }}</p>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
	<!-- Change Password -->
	<div class="col-xl-6 mb-4">
		<div class="card">
			<div class="card-header pb-0 border-0">
				<h5 class="mb-0">
					<i data-feather="lock" style="width: 18px; height: 18px;" class="me-2"></i>Change Password
				</h5>
			</div>
			<div class="card-body">
				@if(session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ session('success') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif
				@if($errors->any())
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<ul class="mb-0">
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif
				<form action="{{ route('teacher.profile.update-password') }}" method="POST">
					@csrf
					@method('PUT')
					<div class="mb-3">
						<label class="form-label">Current Password <span class="text-danger">*</span></label>
						<input type="password" name="current_password" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">New Password <span class="text-danger">*</span></label>
						<input type="password" name="password" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
						<input type="password" name="password_confirmation" class="form-control" required>
					</div>
					<button type="submit" class="btn btn-primary">
						<i data-feather="save" style="width: 14px; height: 14px;" class="me-1"></i> Update Password
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
