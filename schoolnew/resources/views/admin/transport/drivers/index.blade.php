@extends('layouts.app')

@section('title', 'Drivers')

@section('page-title', 'Transport - Drivers')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.vehicles.index') }}">Transport</a></li>
    <li class="breadcrumb-item active">Drivers</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>All Drivers</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.drivers.trash') }}" class="btn btn-outline-danger">
                            <i data-feather="trash-2" class="me-1"></i> Trash
                        </a>
                        <a href="{{ route('admin.drivers.export', request()->query()) }}" class="btn btn-outline-success">
                            <i data-feather="download" class="me-1"></i> Export Excel
                        </a>
                        <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
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

                <!-- Filters -->
                <form action="{{ route('admin.drivers.index') }}" method="GET" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Name, ID, Phone..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">License</label>
                            <select name="license_status" class="form-select">
                                <option value="">All</option>
                                <option value="valid" {{ request('license_status') === 'valid' ? 'selected' : '' }}>Valid</option>
                                <option value="expiring" {{ request('license_status') === 'expiring' ? 'selected' : '' }}>Expiring</option>
                                <option value="expired" {{ request('license_status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-filter me-1"></i> Filter
                                </button>
                                @if(request()->hasAny(['search', 'status', 'license_status']))
                                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary" title="Reset">
                                        <i class="icon-reload"></i>
                                    </a>
                                @endif
                                <button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
                                    <i class="icon-trash me-1"></i> Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>License</th>
                                    <th>License Expiry</th>
                                    <th>Vehicles</th>
                                    <th>Status</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($drivers as $driver)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="ids[]" value="{{ $driver->id }}" class="form-check-input row-checkbox">
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary">{{ $driver->employee_id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($driver->photo)
                                                    <img src="{{ asset('storage/' . $driver->photo) }}" alt="{{ $driver->full_name }}" class="rounded-circle me-2" width="40" height="40">
                                                @else
                                                    <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i data-feather="user" class="text-muted" style="width: 20px;"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $driver->full_name }}</strong>
                                                    @if($driver->email)
                                                        <br><small class="text-muted">{{ $driver->email }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $driver->phone }}
                                            @if($driver->alternate_phone)
                                                <br><small class="text-muted">{{ $driver->alternate_phone }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $driver->getLicenseStatusBadgeClass() }}">{{ $driver->getLicenseStatusLabel() }}</span>
                                            <br><small>{{ $driver->license_number }}</small>
                                        </td>
                                        <td>{{ $driver->license_expiry->format('M d, Y') }}</td>
                                        <td>
                                            @if($driver->vehicles->count() > 0)
                                                @foreach($driver->vehicles as $vehicle)
                                                    <span class="badge badge-light-info">{{ $vehicle->vehicle_no }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($driver->is_active)
                                                <span class="badge badge-light-success">Active</span>
                                            @else
                                                <span class="badge badge-light-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="common-align gap-2 justify-content-start">
                                                <a class="square-white" href="{{ route('admin.drivers.show', $driver) }}" title="View">
                                                    <svg>
                                                        <use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use>
                                                    </svg>
                                                </a>
                                                <a class="square-white" href="{{ route('admin.drivers.edit', $driver) }}" title="Edit">
                                                    <svg>
                                                        <use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $driver->full_name }}">
                                                        <svg>
                                                            <use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i data-feather="users" class="mb-2" style="width: 48px; height: 48px;"></i>
                                                <p>No drivers found.</p>
                                                <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary btn-sm">Add First Driver</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                @if($drivers->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $drivers->withQueryString()->links() }}
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
    // Clear checkboxes on page load
    jQuery('#selectAll').prop('checked', false).prop('indeterminate', false);
    jQuery('.row-checkbox').prop('checked', false);

    function updateBulkState() {
        var checkedCount = jQuery('.row-checkbox:checked').length;
        var totalCount = jQuery('.row-checkbox').length;
        jQuery('#selectedCount').text(checkedCount);

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
        jQuery('.row-checkbox').prop('checked', jQuery(this).is(':checked'));
        updateBulkState();
    });

    jQuery(document).on('change', '.row-checkbox', function() {
        updateBulkState();
    });

    // Bulk delete via AJAX
    jQuery(document).on('click', '#bulkDeleteBtn', function() {
        var selectedIds = [];
        var selectedNames = [];

        jQuery('.row-checkbox:checked').each(function() {
            selectedIds.push(jQuery(this).val());
            var row = jQuery(this).closest('tr');
            selectedNames.push(row.find('strong').first().text().trim());
        });

        if (selectedIds.length === 0) return;

        var namesText = selectedIds.length <= 5
            ? selectedNames.join(', ')
            : selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

        Swal.fire({
            title: 'Move to Trash?',
            html: 'You are about to move <strong>' + selectedIds.length + '</strong> driver(s) to trash:<br><br><small>' + namesText + '</small><br><br><small class="text-muted">You can restore them later from the trash.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, move to trash',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.drivers.bulk-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Moving to Trash...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: function() { Swal.showLoading(); }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Moved to Trash!',
                            text: response.message || 'Drivers moved to trash successfully.',
                            confirmButtonColor: '#3085d6'
                        }).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'An error occurred.'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush
