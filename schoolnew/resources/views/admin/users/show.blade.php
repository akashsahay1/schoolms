@extends('layouts.app')

@section('title', 'User Details')

@section('page-title', 'User Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
	<li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-4 col-12">
		<div class="card">
			<div class="card-body text-center">
				<div class="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center text-white" style="width: 120px; height: 120px; font-size: 48px;">
					{{ strtoupper(substr($user->name, 0, 1)) }}
				</div>
				<h4 class="mb-1">{{ $user->name }}</h4>
				<p class="text-muted mb-3">{{ $user->email }}</p>

				@foreach($user->roles as $role)
					<span class="badge badge-light-{{ $role->name === 'Super Admin' ? 'danger' : ($role->name === 'Admin' ? 'primary' : 'secondary') }} fs-6">
						{{ $role->name }}
					</span>
				@endforeach

				<hr class="my-4">

				<div class="d-grid gap-2">
					<a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
						<i data-feather="edit" class="me-1"></i> Edit User
					</a>
					<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
						<i data-feather="arrow-left" class="me-1"></i> Back to List
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-8 col-12">
		<div class="card">
			<div class="card-header">
				<h5>Account Information</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-borderless">
						<tbody>
							<tr>
								<td class="text-muted" style="width: 200px;">User ID</td>
								<td><strong>#{{ $user->id }}</strong></td>
							</tr>
							<tr>
								<td class="text-muted">Full Name</td>
								<td>{{ $user->name }}</td>
							</tr>
							<tr>
								<td class="text-muted">Email Address</td>
								<td>{{ $user->email }}</td>
							</tr>
							<tr>
								<td class="text-muted">Role</td>
								<td>
									@foreach($user->roles as $role)
										<span class="badge badge-light-{{ $role->name === 'Super Admin' ? 'danger' : ($role->name === 'Admin' ? 'primary' : 'secondary') }}">
											{{ $role->name }}
										</span>
									@endforeach
								</td>
							</tr>
							<tr>
								<td class="text-muted">Email Verified</td>
								<td>
									@if($user->email_verified_at)
										<span class="badge badge-light-success">Verified</span>
										<small class="text-muted ms-2">{{ $user->email_verified_at->format('M d, Y H:i') }}</small>
									@else
										<span class="badge badge-light-warning">Not Verified</span>
									@endif
								</td>
							</tr>
							<tr>
								<td class="text-muted">Account Created</td>
								<td>{{ $user->created_at->format('M d, Y H:i:s') }}</td>
							</tr>
							<tr>
								<td class="text-muted">Last Updated</td>
								<td>{{ $user->updated_at->format('M d, Y H:i:s') }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h5>Permissions</h5>
			</div>
			<div class="card-body">
				@php
					$permissions = $user->getAllPermissions();
				@endphp

				@if($permissions->count() > 0)
					<div class="row">
						@foreach($permissions->chunk(ceil($permissions->count() / 3)) as $chunk)
							<div class="col-md-4">
								<ul class="list-unstyled">
									@foreach($chunk as $permission)
										<li class="mb-2">
											<i data-feather="check" class="text-success me-2" style="width: 16px; height: 16px;"></i>
											{{ ucwords(str_replace('_', ' ', $permission->name)) }}
										</li>
									@endforeach
								</ul>
							</div>
						@endforeach
					</div>
				@else
					<p class="text-muted mb-0">No permissions assigned.</p>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
