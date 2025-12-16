@extends('layouts.app')

@section('title', 'Add New User')

@section('page-title', 'Add New User')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
	<li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-8 col-12">
		<div class="card">
			<div class="card-header">
				<h5>Create New User</h5>
			</div>
			<div class="card-body">
				<form method="POST" action="{{ route('admin.users.store') }}">
					@csrf

					<div class="mb-3">
						<label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter full name">
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
						<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter email address">
						@error('email')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label" for="role">Role <span class="text-danger">*</span></label>
						<select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
							<option value="">Select Role</option>
							@foreach($roles as $role)
								<option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
									{{ $role->name }}
								</option>
							@endforeach
						</select>
						@error('role')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label" for="password">Password <span class="text-danger">*</span></label>
						<div class="input-group">
							<input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Enter password">
							<button class="btn btn-outline-secondary" type="button" id="togglePassword">
								<i data-feather="eye"></i>
							</button>
						</div>
						@error('password')
							<div class="invalid-feedback d-block">{{ $message }}</div>
						@enderror
						<small class="text-muted">Password must be at least 8 characters.</small>
					</div>

					<div class="mb-4">
						<label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
						<div class="input-group">
							<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm password">
							<button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
								<i data-feather="eye"></i>
							</button>
						</div>
					</div>

					<div class="d-flex justify-content-between">
						<a href="{{ route('admin.users.index') }}" class="btn btn-light">
							<i data-feather="arrow-left" class="me-1"></i> Back
						</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Create User
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-4 col-12">
		<div class="card">
			<div class="card-header">
				<h5>Role Permissions</h5>
			</div>
			<div class="card-body">
				<p class="text-muted mb-3">Each role has different access levels:</p>
				<ul class="list-unstyled">
					<li class="mb-2">
						<span class="badge badge-light-danger">Super Admin</span>
						<small class="d-block text-muted">Full system access</small>
					</li>
					<li class="mb-2">
						<span class="badge badge-light-primary">Admin</span>
						<small class="d-block text-muted">Most permissions except role management</small>
					</li>
					<li class="mb-2">
						<span class="badge badge-light-info">Teacher</span>
						<small class="d-block text-muted">View students, mark attendance, manage homework</small>
					</li>
					<li class="mb-2">
						<span class="badge badge-light-success">Accountant</span>
						<small class="d-block text-muted">Fee collection and financial reports</small>
					</li>
					<li class="mb-2">
						<span class="badge badge-light-warning">Librarian</span>
						<small class="d-block text-muted">Library management</small>
					</li>
					<li class="mb-2">
						<span class="badge badge-light-secondary">Student</span>
						<small class="d-block text-muted">View own information</small>
					</li>
					<li class="mb-2">
						<span class="badge badge-light-secondary">Parent</span>
						<small class="d-block text-muted">View children's information</small>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	document.getElementById('togglePassword').addEventListener('click', function() {
		const passwordInput = document.getElementById('password');
		const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
		passwordInput.setAttribute('type', type);
	});

	document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
		const passwordInput = document.getElementById('password_confirmation');
		const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
		passwordInput.setAttribute('type', type);
	});
</script>
@endpush
