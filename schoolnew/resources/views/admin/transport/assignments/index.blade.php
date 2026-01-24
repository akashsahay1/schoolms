@extends('layouts.app')

@section('title', 'Route Assignments')

@section('page-title', 'Transport - Route Assignments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Transport</li>
    <li class="breadcrumb-item active">Route Assignments</li>
@endsection

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-sm-6 col-xl-4">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round primary">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#user') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $totalStudentsWithTransport }}</h4>
                        <span class="f-light">Students with Transport</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round success">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#task') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $activeAssignments }}</h4>
                        <span class="f-light">Active Assignments</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round secondary">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#maps') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $totalAssignments }}</h4>
                        <span class="f-light">Total Assignments</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Route Assignments</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
                            <i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
                        </button>
                        <a href="{{ route('admin.transport.assignments.trash') }}" class="btn btn-outline-danger position-relative">
                            <i data-feather="trash" class="me-1"></i> Trash
                            @if($trashedCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $trashedCount > 99 ? '99+' : $trashedCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.transport.assignments.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Assign Route
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.transport.assignments.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="route" class="form-select">
                                <option value="">All Routes</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" {{ request('route') == $route->id ? 'selected' : '' }}>
                                        {{ $route->route_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="class" class="form-select">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="filter" class="me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll" title="Select All">
                                </th>
                                <th>#</th>
                                <th>Student</th>
                                <th>Class/Section</th>
                                <th>Route</th>
                                <th>Vehicle</th>
                                <th>Pickup Point</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assignment)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input assignment-checkbox" value="{{ $assignment->id }}" data-name="{{ $assignment->student->first_name ?? 'N/A' }} {{ $assignment->student->last_name ?? '' }}">
                                    </td>
                                    <td>{{ $assignments->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $assignment->student->first_name ?? 'N/A' }} {{ $assignment->student->last_name ?? '' }}</strong>
                                        <br><small class="text-muted">{{ $assignment->student->admission_no ?? '' }}</small>
                                    </td>
                                    <td>
                                        {{ $assignment->student->schoolClass->name ?? '-' }}
                                        @if($assignment->student->section)
                                            <span class="text-muted">/ {{ $assignment->student->section->name }}</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $assignment->route->route_name ?? '-' }}</strong></td>
                                    <td>
                                        @if($assignment->route && $assignment->route->vehicle)
                                            <span class="badge badge-light-info">{{ $assignment->route->vehicle->vehicle_no }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $assignment->pickup_point ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-light-{{ $assignment->is_active ? 'success' : 'danger' }}">
                                            {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            <a class="square-white" href="{{ route('admin.transport.assignments.edit', $assignment) }}" title="Edit">
                                                <svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
                                            </a>
                                            <form action="{{ route('admin.transport.assignments.destroy', $assignment) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $assignment->student->first_name ?? 'N/A' }} {{ $assignment->student->last_name ?? '' }}">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <p class="text-muted mb-2">No route assignments found.</p>
                                        <a href="{{ route('admin.transport.assignments.create') }}" class="btn btn-primary">Assign First Route</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assignments->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $assignments->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    const selectAllCheckbox = jQuery('#selectAll');
    const assignmentCheckboxes = jQuery('.assignment-checkbox');
    const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
    const selectedCountSpan = jQuery('#selectedCount');

    function updateBulkDeleteState() {
        const checkedCount = jQuery('.assignment-checkbox:checked').length;
        selectedCountSpan.text(checkedCount);

        if (checkedCount > 0) {
            bulkDeleteBtn.removeClass('d-none');
        } else {
            bulkDeleteBtn.addClass('d-none');
        }

        const totalCheckboxes = assignmentCheckboxes.length;
        if (totalCheckboxes > 0 && checkedCount === totalCheckboxes) {
            selectAllCheckbox.prop('checked', true);
            selectAllCheckbox.prop('indeterminate', false);
        } else if (checkedCount > 0) {
            selectAllCheckbox.prop('checked', false);
            selectAllCheckbox.prop('indeterminate', true);
        } else {
            selectAllCheckbox.prop('checked', false);
            selectAllCheckbox.prop('indeterminate', false);
        }
    }

    selectAllCheckbox.on('change', function() {
        assignmentCheckboxes.prop('checked', jQuery(this).is(':checked'));
        updateBulkDeleteState();
    });

    assignmentCheckboxes.on('change', function() {
        updateBulkDeleteState();
    });

    bulkDeleteBtn.on('click', function() {
        const selectedIds = [];
        const selectedNames = [];

        jQuery('.assignment-checkbox:checked').each(function() {
            selectedIds.push(jQuery(this).val());
            selectedNames.push(jQuery(this).data('name'));
        });

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select at least one assignment to delete.'
            });
            return;
        }

        const namesText = selectedIds.length <= 5
            ? selectedNames.join(', ')
            : selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

        Swal.fire({
            title: 'Move to Trash?',
            html: `You are about to move <strong>${selectedIds.length}</strong> assignment(s) to trash:<br><br><small>${namesText}</small><br><br><small class="text-muted">You can restore them later from the trash.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, move to trash',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.transport.assignments.bulk-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Moving to Trash...',
                            text: 'Please wait.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Moved to Trash!',
                            text: response.message,
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'An error occurred.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message
                        });
                    }
                });
            }
        });
    });

    jQuery(document).on('click', '.move-to-trash', function(e) {
        e.preventDefault();
        var form = jQuery(this).closest('form');
        var itemName = jQuery(this).data('name') || 'this assignment';

        Swal.fire({
            title: 'Move to Trash?',
            html: `You are about to move assignment for <strong>${itemName}</strong> to trash.<br><small class="text-muted">You can restore it later from the trash.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FC4438',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, move to trash',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
