@extends('layouts.app')

@section('title', 'Roles Management')

@section('page-title', 'Roles Management')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Roles & Permissions</li>
@endsection

@push('styles')
<style>
.role-card {
	border: 1px solid #e9ecef;
	border-radius: 10px;
	transition: all 0.3s ease;
	overflow: hidden;
}
.role-card:hover {
	box-shadow: 0 4px 15px rgba(0,0,0,0.1);
	transform: translateY(-2px);
}
.role-card .role-header {
	padding: 20px;
	border-bottom: none;
}
.role-card .role-header,
.role-card .role-header h6,
.role-card .role-header span,
.role-card .role-header i,
.role-card .role-header .badge {
	color: #fff !important;
}
.role-card .role-body {
	padding: 20px;
}
.role-icon {
	width: 50px;
	height: 50px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 22px;
}
.permission-tag {
	display: inline-block;
	padding: 3px 10px;
	border-radius: 20px;
	font-size: 12px;
	font-weight: 500;
	margin: 2px;
}
</style>
@endpush

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

		<!-- Header -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h5 class="mb-1">Roles & Permissions</h5>
						<p class="text-muted mb-0">Manage user roles and control what each role can access</p>
					</div>
					<a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Add New
					</a>
				</div>
			</div>
		</div>

		<!-- Role Cards Grid -->
		<div class="row">
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
			@endphp

			@foreach($roles as $role)
				@php
					$color = $roleColors[$role->name] ?? $defaultColor;
					$protectedRoles = ['Super Admin', 'Admin', 'Student', 'Parent', 'Teacher'];
					$isProtected = in_array($role->name, $protectedRoles);
				@endphp
				<div class="col-xl-4 col-md-6 mb-4">
					<div class="role-card bg-white h-100">
						<div class="role-header" style="background: {{ $color['bg'] }};">
							<div class="d-flex align-items-center justify-content-between">
								<div class="d-flex align-items-center gap-3">
									<div class="role-icon" style="background: rgba(255,255,255,0.2); color: #fff;">
										<i class="fa-solid {{ $color['icon'] }}"></i>
									</div>
									<div>
										<h6 class="mb-1 fw-bold" style="color: #fff;">{{ $role->name }}</h6>
										<div class="d-flex gap-2">
											<span class="badge" style="background: rgba(255,255,255,0.2); color: #fff;">
												{{ $role->permissions_count }} Permissions
											</span>
											<span class="badge" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9);">
												{{ $role->users_count }} {{ Str::plural('User', $role->users_count) }}
											</span>
										</div>
									</div>
								</div>
								@if($isProtected)
									<span class="badge" style="background: rgba(255,255,255,0.2); color: #fff;" title="System role"><i class="fa-solid fa-lock" style="font-size: 11px;"></i></span>
								@endif
							</div>
						</div>
						<div class="role-body">
							<div class="mb-3">
								<small class="text-muted fw-semibold d-block mb-2">KEY PERMISSIONS</small>
								<div>
									@php
										$permNames = $role->permissions->pluck('name')->take(6);
										$remaining = $role->permissions_count - 6;
									@endphp
									@forelse($permNames as $perm)
										<span class="permission-tag" style="background: {{ $color['light'] }}; color: {{ $color['bg'] }};">{{ ucfirst($perm) }}</span>
									@empty
										<span class="text-muted">No permissions assigned</span>
									@endforelse
									@if($remaining > 0)
										<span class="permission-tag bg-light text-muted">+{{ $remaining }} more</span>
									@endif
								</div>
							</div>
							<div class="d-flex gap-2 pt-2 border-top">
								<a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-outline-primary flex-fill">
									<i class="fa-solid fa-eye me-1"></i> View
								</a>
								<a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary flex-fill">
									<i class="fa-solid fa-pen-to-square me-1"></i> Edit
								</a>
								@if(!$isProtected && $role->users_count == 0)
									<form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="delete-form flex-fill">
										@csrf
										@method('DELETE')
										<button type="button" class="btn btn-sm btn-outline-danger w-100 delete-confirm" data-name="{{ $role->name }}">
											<i class="fa-solid fa-trash me-1"></i> Delete
										</button>
									</form>
								@endif
							</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>

		<!-- Pagination -->
		<div class="d-flex justify-content-center mt-2">
			{{ $roles->withQueryString()->links() }}
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	// Single delete handler
	jQuery(document).on('click', '.delete-confirm', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		var itemName = jQuery(this).data('name') || 'this role';

		Swal.fire({
			title: 'Delete Role?',
			html: 'You are about to permanently delete <strong>' + itemName + '</strong>.<br><small class="text-danger">This action cannot be undone.</small>',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#FC4438',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, delete',
			cancelButtonText: 'Cancel',
			reverseButtons: true
		}).then(function(result) {
			if (result.isConfirmed) {
				form.submit();
			}
		});
	});
});
</script>
@endpush
