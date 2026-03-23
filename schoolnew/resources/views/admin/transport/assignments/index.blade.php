@extends('layouts.app')

@section('title', 'Route Assignments')

@section('page-title', 'Transport - Route Assignments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Transport</li>
    <li class="breadcrumb-item active">Route Assignments</li>
@endsection

@push('styles')
<style>
    .assign-stat {
        border: none;
        border-radius: 14px;
        transition: transform 0.15s ease;
    }
    .assign-stat:hover {
        transform: translateY(-2px);
    }
    .assign-stat .card-body {
        padding: 1.5rem;
    }
    .assign-stat .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .assign-stat .stat-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #7f8c8d;
        margin-bottom: 4px;
        font-weight: 500;
    }
    .assign-stat .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .student-cell .name {
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.3;
    }
    .student-cell .admission {
        font-size: 11px;
        color: #95a5a6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        <div class="col-xl-4 col-md-6">
            <div class="card assign-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(115, 102, 255, 0.08);">
                            <i class="icon-user" style="font-size: 24px; color: #7366ff;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Students with Transport</div>
                            <div class="stat-value" style="color: #7366ff;">{{ $totalStudentsWithTransport }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card assign-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(39, 174, 96, 0.08);">
                            <i class="icon-check" style="font-size: 24px; color: #27ae60;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Active Assignments</div>
                            <div class="stat-value text-success">{{ $activeAssignments }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card assign-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(52, 152, 219, 0.08);">
                            <i class="icon-direction-alt" style="font-size: 24px; color: #3498db;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Total Assignments</div>
                            <div class="stat-value text-primary">{{ $totalAssignments }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" role="alert" style="font-size: 13px; border-radius: 8px;">
            <i class="icon-check me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" role="alert" style="font-size: 13px; border-radius: 8px;">
            <i class="icon-alert me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold">Route Assignments</h6>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
                        <i class="icon-trash me-1"></i> Delete Selected
                    </button>
                    <a href="{{ route('admin.transport.assignments.trash') }}" class="btn btn-outline-danger position-relative">
                        <i class="icon-trash me-1"></i> Trash
                        @if($trashedCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $trashedCount > 99 ? '99+' : $trashedCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.transport.assignments.create') }}" class="btn btn-primary">
                        <i class="icon-plus me-1"></i> Assign Route
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form action="{{ route('admin.transport.assignments.index') }}" method="GET" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label">Search Student</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or Admission No..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Route</label>
                    <select name="route" class="form-select">
                        <option value="">All Routes</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" {{ request('route') == $route->id ? 'selected' : '' }}>
                                {{ $route->route_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Class</label>
                    <select name="class" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="icon-filter me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'route', 'class', 'status']))
                            <a href="{{ route('admin.transport.assignments.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="icon-reload"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll" autocomplete="off">
                            </th>
                            <th style="width: 4%;">#</th>
                            <th style="width: 20%;">Student</th>
                            <th style="width: 12%;">Class</th>
                            <th style="width: 15%;">Route</th>
                            <th style="width: 12%;">Vehicle</th>
                            <th style="width: 13%;">Pickup Point</th>
                            <th style="width: 8%;" class="text-center">Status</th>
                            <th style="width: 10%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input assignment-checkbox" value="{{ $assignment->id }}" data-name="{{ $assignment->student->first_name ?? 'N/A' }} {{ $assignment->student->last_name ?? '' }}" autocomplete="off">
                                </td>
                                <td class="text-muted">{{ $assignments->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="student-cell">
                                        <div class="name">{{ $assignment->student->first_name ?? 'N/A' }} {{ $assignment->student->last_name ?? '' }}</div>
                                        <div class="admission">{{ $assignment->student->admission_no ?? '' }}</div>
                                    </div>
                                </td>
                                <td>
                                    {{ $assignment->student->schoolClass->name ?? '-' }}
                                    @if($assignment->student->section)
                                        <span class="text-muted">({{ $assignment->student->section->name }})</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $assignment->route->route_name ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($assignment->route && $assignment->route->vehicle)
                                        <span class="badge badge-light-info px-2">{{ $assignment->route->vehicle->vehicle_no }}</span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>{{ $assignment->pickup_point ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-light-{{ $assignment->is_active ? 'success' : 'danger' }} px-2">
                                        {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="common-align gap-2 justify-content-center">
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
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                            <i class="icon-direction-alt" style="font-size: 28px; color: #95a5a6;"></i>
                                        </div>
                                        <h6 class="mb-1">No Assignments Found</h6>
                                        <p class="text-muted mb-3">No route assignments match the selected filters.</p>
                                        <a href="{{ route('admin.transport.assignments.create') }}" class="btn btn-primary btn-sm">
                                            <i class="icon-plus me-1"></i> Assign First Route
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($assignments->hasPages())
            <div class="card-footer bg-white">
                {{ $assignments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Clear checkboxes on page load
    jQuery('#selectAll').prop('checked', false).prop('indeterminate', false);
    jQuery('.assignment-checkbox').prop('checked', false);

    function updateBulkState() {
        var checkedCount = jQuery('.assignment-checkbox:checked').length;
        var totalCount = jQuery('.assignment-checkbox').length;

        if (checkedCount > 0) {
            jQuery('#bulkDeleteBtn').removeClass('d-none');
        } else {
            jQuery('#bulkDeleteBtn').addClass('d-none');
        }

        if (totalCount > 0 && checkedCount === totalCount) {
            jQuery('#selectAll').prop('checked', true).prop('indeterminate', false);
        } else if (checkedCount > 0) {
            jQuery('#selectAll').prop('checked', false).prop('indeterminate', true);
        } else {
            jQuery('#selectAll').prop('checked', false).prop('indeterminate', false);
        }
    }

    jQuery(document).on('change', '#selectAll', function() {
        jQuery('.assignment-checkbox').prop('checked', jQuery(this).is(':checked'));
        updateBulkState();
    });

    jQuery(document).on('change', '.assignment-checkbox', function() {
        updateBulkState();
    });

    // Bulk Delete
    jQuery(document).on('click', '#bulkDeleteBtn', function() {
        var selectedIds = [];
        var selectedNames = [];

        jQuery('.assignment-checkbox:checked').each(function() {
            selectedIds.push(jQuery(this).val());
            selectedNames.push(jQuery(this).data('name'));
        });

        if (selectedIds.length === 0) return;

        var namesText = selectedIds.length <= 5
            ? selectedNames.join(', ')
            : selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

        Swal.fire({
            title: 'Move to Trash?',
            html: 'You are about to move <strong>' + selectedIds.length + '</strong> assignment(s) to trash:<br><br><small>' + namesText + '</small><br><br><small class="text-muted">You can restore them later from the trash.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, move to trash',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.transport.assignments.bulk-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    beforeSend: function() {
                        Swal.fire({ title: 'Moving to Trash...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
                    },
                    success: function(response) {
                        Swal.fire({ icon: 'success', title: 'Moved to Trash!', text: response.message, confirmButtonColor: '#3085d6' }).then(function() { window.location.reload(); });
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseJSON?.message || 'An error occurred.' });
                    }
                });
            }
        });
    });

    // Single Move to Trash
    jQuery(document).on('click', '.move-to-trash', function(e) {
        e.preventDefault();
        var form = jQuery(this).closest('form');
        var itemName = jQuery(this).data('name') || 'this assignment';

        Swal.fire({
            title: 'Move to Trash?',
            html: 'You are about to move assignment for <strong>' + itemName + '</strong> to trash.<br><small class="text-muted">You can restore it later from the trash.</small>',
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
});
</script>
@endpush
