@extends('layouts.app')

@section('title', 'Edit Role')

@section('page-title', 'Edit Role')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
	<li class="breadcrumb-item active">Edit {{ $role->name }}</li>
@endsection

@push('styles')
<style>
.permission-group-card {
	border: 1px solid #e9ecef;
	border-radius: 10px;
	overflow: hidden;
	transition: all 0.2s ease;
}
.permission-group-card:hover {
	border-color: var(--theme-default);
	box-shadow: 0 2px 8px rgba(115, 102, 255, 0.1);
}
.permission-group-header {
	padding: 12px 16px;
	background: #f8f9fa;
	border-bottom: 1px solid #e9ecef;
	cursor: pointer;
}
.permission-group-header:hover {
	background: #f0f1f3;
}
.permission-group-body {
	padding: 12px 16px;
}
.permission-item {
	padding: 6px 0;
	border-bottom: 1px solid #f5f5f5;
}
.permission-item:last-child {
	border-bottom: none;
}
.permission-item label {
	cursor: pointer;
	user-select: none;
}
.permission-item .form-check-input {
	width: 18px;
	height: 18px;
	margin-top: 2px;
	cursor: pointer;
}
.permission-item .form-check-input:checked {
	background-color: var(--theme-default);
	border-color: var(--theme-default);
}
.group-checkbox-wrapper .form-check-input {
	width: 18px;
	height: 18px;
	cursor: pointer;
}
.group-checkbox-wrapper .form-check-input:checked {
	background-color: var(--theme-default);
	border-color: var(--theme-default);
}
.group-checkbox-wrapper .form-check-input:indeterminate {
	background-color: var(--theme-default);
	border-color: var(--theme-default);
}
.permission-count-badge {
	font-size: 11px;
	padding: 3px 8px;
	border-radius: 20px;
}
</style>
@endpush

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<form action="{{ route('admin.roles.update', $role) }}" method="POST">
			@csrf
			@method('PUT')

			<!-- Role Name -->
			<div class="card">
				<div class="card-header">
					<h5>Edit Role: {{ $role->name }}</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" placeholder="Enter role name" required {{ $role->name === 'Super Admin' ? 'readonly' : '' }}>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							@if($role->name === 'Super Admin')
								<small class="text-muted">Super Admin role name cannot be changed.</small>
							@endif
						</div>
						<div class="col-md-6 d-flex align-items-end">
							<div class="d-flex gap-2 align-items-center text-muted">
								<span>Selected: <strong class="text-primary" id="selectedPermCount">{{ count($rolePermissions) }}</strong> / {{ $permissions->count() }} permissions</span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Permissions -->
			<div class="card">
				<div class="card-header">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">Permissions</h5>
						<div class="d-flex gap-2">
							<button type="button" class="btn btn-sm btn-primary" id="selectAllPermissions">
								<i class="fa-solid fa-check-double me-1"></i> Select All
							</button>
							<button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPermissions">
								<i class="fa-solid fa-xmark me-1"></i> Deselect All
							</button>
						</div>
					</div>
				</div>
				<div class="card-body">
					<p class="text-muted mb-4">Check the permissions you want to assign to this role. Use group checkboxes to toggle all permissions in a group.</p>

					@php
						$groupColors = [
							'Dashboard' => '#7366FF',
							'Students' => '#0D6EFD',
							'Parents' => '#6F42C1',
							'Staff' => '#FF5F15',
							'Academic Year' => '#33BFBF',
							'Classes' => '#54BA4A',
							'Sections' => '#20C997',
							'Subjects' => '#198754',
							'Attendance' => '#FFB829',
							'Exams' => '#E83E8C',
							'Results' => '#DC3545',
							'Marks' => '#FD7E14',
							'Homework' => '#0DCAF0',
							'Fees' => '#FFB829',
							'Fee Structure' => '#FF9F43',
							'Fee Reports' => '#E83E8C',
							'Library' => '#33BFBF',
							'Books' => '#20C997',
							'Transport' => '#6c757d',
							'Vehicles' => '#ADB5BD',
							'Routes' => '#495057',
							'Notices' => '#7366FF',
							'Events' => '#54BA4A',
							'Messages' => '#0D6EFD',
							'Reports' => '#DC3545',
							'Settings' => '#6c757d',
							'Users' => '#FF5F15',
							'Roles' => '#E83E8C',
						];
						$defaultGroupColor = '#6c757d';
					@endphp

					<div class="row">
						@foreach($permissionGroups as $group => $groupPermissions)
							@php $gColor = $groupColors[$group] ?? $defaultGroupColor; @endphp
							<div class="col-md-6 col-lg-4 mb-3">
								<div class="permission-group-card h-100">
									<div class="permission-group-header d-flex align-items-center justify-content-between group-checkbox-wrapper">
										<div class="d-flex align-items-center gap-2">
											<input type="checkbox" class="form-check-input group-checkbox" data-group="{{ Str::slug($group) }}" id="group-{{ Str::slug($group) }}">
											<label for="group-{{ Str::slug($group) }}" class="fw-semibold mb-0" style="cursor: pointer;">{{ $group }}</label>
										</div>
										<span class="permission-count-badge text-white" style="background: {{ $gColor }};">{{ count($groupPermissions) }}</span>
									</div>
									<div class="permission-group-body">
										@foreach($groupPermissions as $permission)
											<div class="permission-item d-flex align-items-center gap-2">
												<input type="checkbox" class="form-check-input permission-checkbox permission-{{ Str::slug($group) }}" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
												<label class="form-check-label mb-0" for="permission-{{ $permission->id }}">
													{{ ucfirst($permission->name) }}
												</label>
											</div>
										@endforeach
									</div>
								</div>
							</div>
						@endforeach
					</div>

					@error('permissions')
						<div class="text-danger mt-2">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<!-- Actions -->
			<div class="card">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<a href="{{ route('admin.roles.index') }}" class="btn btn-light">
							<i class="fa-solid fa-arrow-left me-1"></i> Back to Roles
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="fa-solid fa-floppy-disk me-1"></i> Update Role
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	function updateSelectedCount() {
		var count = jQuery('.permission-checkbox:checked').length;
		jQuery('#selectedPermCount').text(count);
	}

	// Update group checkbox state based on permissions
	function updateGroupCheckbox(group) {
		var checkboxes = jQuery('.permission-' + group);
		var checkedCount = checkboxes.filter(':checked').length;
		var totalCount = checkboxes.length;
		var groupCheckbox = jQuery('#group-' + group);

		if (checkedCount === 0) {
			groupCheckbox.prop('checked', false);
			groupCheckbox.prop('indeterminate', false);
		} else if (checkedCount === totalCount) {
			groupCheckbox.prop('checked', true);
			groupCheckbox.prop('indeterminate', false);
		} else {
			groupCheckbox.prop('checked', false);
			groupCheckbox.prop('indeterminate', true);
		}
	}

	// Initialize all group checkboxes
	jQuery('.group-checkbox').each(function() {
		updateGroupCheckbox(jQuery(this).data('group'));
	});

	// Group checkbox change handler
	jQuery(document).on('change', '.group-checkbox', function() {
		var group = jQuery(this).data('group');
		var isChecked = jQuery(this).is(':checked');
		jQuery('.permission-' + group).prop('checked', isChecked);
		updateSelectedCount();
	});

	// Individual permission checkbox change handler
	jQuery(document).on('change', '.permission-checkbox', function() {
		var classes = jQuery(this).attr('class').split(' ');
		for (var i = 0; i < classes.length; i++) {
			if (classes[i].indexOf('permission-') === 0 && classes[i] !== 'permission-checkbox') {
				var group = classes[i].replace('permission-', '');
				updateGroupCheckbox(group);
				break;
			}
		}
		updateSelectedCount();
	});

	// Select All button
	jQuery('#selectAllPermissions').on('click', function() {
		jQuery('.permission-checkbox').prop('checked', true);
		jQuery('.group-checkbox').prop('checked', true).prop('indeterminate', false);
		updateSelectedCount();
	});

	// Deselect All button
	jQuery('#deselectAllPermissions').on('click', function() {
		jQuery('.permission-checkbox').prop('checked', false);
		jQuery('.group-checkbox').prop('checked', false).prop('indeterminate', false);
		updateSelectedCount();
	});

	// Initial count
	updateSelectedCount();
});
</script>
@endpush
