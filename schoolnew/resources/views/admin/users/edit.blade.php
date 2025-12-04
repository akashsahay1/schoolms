@extends('layouts.app')

@section('title', 'Edit User')

@section('page-title', 'Edit User')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
	<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-8 col-12">
		<div class="card">
			<div class="card-header">
				<h5>Edit User: {{ $user->name }}</h5>
			</div>
			<div class="card-body">
				<form method="POST" action="{{ route('admin.users.update', $user) }}">
					@csrf
					@method('PUT')

					<div class="mb-3">
						<label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Enter full name">
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
						<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Enter email address">
						@error('email')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label" for="role">Role <span class="text-danger">*</span></label>
						<select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
							<option value="">Select Role</option>
							@foreach($roles as $role)
								<option value="{{ $role->name }}" {{ old('role', $userRole) == $role->name ? 'selected' : '' }}>
									{{ $role->name }}
								</option>
							@endforeach
						</select>
						@error('role')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<hr class="my-4">
					<h6 class="mb-3">Change Password (Optional)</h6>
					<p class="text-muted small">Leave blank to keep the current password.</p>

					<div class="mb-3">
						<label class="form-label" for="password">New Password</label>
						<div class="input-group">
							<input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter new password">
							<button class="btn btn-outline-secondary" type="button" id="togglePassword">
								<i data-feather="eye"></i>
							</button>
						</div>
						@error('password')
							<div class="invalid-feedback d-block">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-4">
						<label class="form-label" for="password_confirmation">Confirm New Password</label>
						<div class="input-group">
							<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
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
							<i data-feather="save" class="me-1"></i> Update User
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-4 col-12">
		<div class="card">
			<div class="card-header">
				<h5>User Information</h5>
			</div>
			<div class="card-body">
				<div class="text-center mb-4">
					<div class="avatar avatar-xl mx-auto mb-3">
						<div class="avatar-title rounded-circle bg-primary" style="width: 80px; height: 80px; font-size: 32px;">
							{{ strtoupper(substr($user->name, 0, 1)) }}
						</div>
					</div>
					<h5 class="mb-1">{{ $user->name }}</h5>
					<p class="text-muted mb-0">{{ $user->email }}</p>
				</div>

				<ul class="list-group list-group-flush">
					<li class="list-group-item d-flex justify-content-between">
						<span>Account Created</span>
						<span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span>Last Updated</span>
						<span class="text-muted">{{ $user->updated_at->format('M d, Y') }}</span>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span>Email Verified</span>
						<span class="text-muted">
							@if($user->email_verified_at)
								<i data-feather="check-circle" class="text-success"></i>
							@else
								<i data-feather="x-circle" class="text-danger"></i>
							@endif
						</span>
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
