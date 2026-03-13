@extends('layouts.app')

@section('title', 'View Role - ' . $role->name)

@section('page-title', 'View Role')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
	<li class="breadcrumb-item active">{{ $role->name }}</li>
@endsection

@push('styles')
<style>
.role-detail-icon {
	width: 70px;
	height: 70px;
	border-radius: 16px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 28px;
}
.permission-group-card {
	border: 1px solid #e9ecef;
	border-radius: 10px;
	overflow: hidden;
}
.permission-group-header {
	padding: 12px 16px;
	border-bottom: 1px solid #e9ecef;
}
.perm-check-item {
	padding: 6px 0;
	border-bottom: 1px solid #f5f5f5;
}
.perm-check-item:last-child {
	border-bottom: none;
}
</style>
@endpush

@section('content')
@php
	$roleColors = [
		'Super Admin' => ['bg' => '#7366FF', 'light' => 'rgba(115, 102, 255, 0.1)', 'icon' => 'fa-crown'],
		'Admin' => ['bg' => '#54BA4A', 'light' => 'rgba(84, 186, 74, 0.1)', 'icon' => 'fa-user-shield'],
		'Teacher' => ['bg' => '#FF5F15', 'light' => 'rgba(255, 95, 21, 0.1)', 'icon' => 'fa-chalkboard-user'],
		'Accountant' => ['bg' => '#FFB829', 'light' => 'rgba(255, 184, 41, 0.1)', 'icon' => 'fa-calculator'],
		'Librarian' => ['bg' => '#33BFBF', 'light' => 'rgba(51, 191, 191, 0.1)', 'icon' => 'fa-book-open'],
		'Receptionist' => ['bg' => '#E83E8C', 'light' => 'rgba(232, 62, 140, 0.1)', 'icon' => 'fa-headset'],
		'Student' => ['bg' => '#0D6EFD', 'light' => 'rgba(13, 110, 253, 0.1)', 'icon' => 'fa-graduation-cap'],
		'Parent' => ['bg' => '#6F42C1', 'light' => 'rgba(111, 66, 193, 0.1)', 'icon' => 'fa-people-roof'],
	];
	$defaultColor = ['bg' => '#6c757d', 'light' => 'rgba(108, 117, 125, 0.1)', 'icon' => 'fa-user-tag'];
	$color = $roleColors[$role->name] ?? $defaultColor;
	$protectedRoles = ['Super Admin', 'Admin', 'Student', 'Parent', 'Teacher'];
	$isProtected = in_array($role->name, $protectedRoles);
@endphp

<div class="row">
	<div class="col-12">
		<!-- Role Header -->
		<div class="card">
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
					<div class="d-flex align-items-center gap-3">
						<div class="role-detail-icon" style="background: {{ $color['light'] }}; color: {{ $color['bg'] }};">
							<i class="fa-solid {{ $color['icon'] }}"></i>
						</div>
						<div>
							<h4 class="mb-1">
								{{ $role->name }}
								@if($isProtected)
									<span class="badge bg-light text-muted ms-1" style="font-size: 11px;"><i class="fa-solid fa-lock me-1"></i>System Role</span>
								@endif
							</h4>
							<div class="d-flex gap-3 text-muted">
								<span><i class="fa-solid fa-key me-1"></i>{{ $role->permissions->count() }} Permissions</span>
								<span><i class="fa-solid fa-users me-1"></i>{{ $usersCount }} {{ Str::plural('User', $usersCount) }}</span>
								<span><i class="fa-solid fa-calendar me-1"></i>Created {{ $role->created_at->format('M d, Y') }}</span>
							</div>
						</div>
					</div>
					<div class="d-flex gap-2">
						<a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
							<i class="fa-solid fa-pen-to-square me-1"></i> Edit Permissions
						</a>
						<a href="{{ route('admin.roles.index') }}" class="btn btn-light">
							<i class="fa-solid fa-arrow-left me-1"></i> Back
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Permissions -->
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">Assigned Permissions ({{ $role->permissions->count() }})</h5>
			</div>
			<div class="card-body">
				@if($role->permissions->count() > 0)
					<div class="row">
						@foreach($permissionGroups as $group => $groupPermissions)
							<div class="col-md-6 col-lg-4 mb-3">
								<div class="permission-group-card h-100">
									<div class="permission-group-header d-flex align-items-center justify-content-between" style="background: {{ $color['light'] }};">
										<span class="fw-semibold" style="color: {{ $color['bg'] }};">{{ $group }}</span>
										<span class="badge text-white" style="background: {{ $color['bg'] }};">{{ count($groupPermissions) }}</span>
									</div>
									<div class="px-3 py-2">
										@foreach($groupPermissions as $permission)
											<div class="perm-check-item d-flex align-items-center gap-2">
												<i class="fa-solid fa-circle-check" style="color: {{ $color['bg'] }}; font-size: 14px;"></i>
												<span>{{ ucfirst($permission->name) }}</span>
											</div>
										@endforeach
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<div class="text-center py-5">
						<i class="fa-solid fa-shield-halved text-muted" style="font-size: 48px;"></i>
						<p class="text-muted mt-3 mb-0">No permissions assigned to this role.</p>
						<a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary mt-3">Assign Permissions</a>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
