@extends('layouts.app')

@section('title', 'Drivers')

@section('page-title', 'Transport - Drivers')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.vehicles.index') }}">Transport</a></li>
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
                            <i data-feather="download" class="me-1"></i> Export CSV
                        </a>
                        <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Add Driver
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
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, ID, phone..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="license_status" class="form-select">
                                <option value="">License Status</option>
                                <option value="valid" {{ request('license_status') === 'valid' ? 'selected' : '' }}>Valid</option>
                                <option value="expiring" {{ request('license_status') === 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
                                <option value="expired" {{ request('license_status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                <form id="bulkDeleteForm" action="{{ route('admin.drivers.bulk-delete') }}" method="POST">
                    @csrf
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

                    @if($drivers->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                                <i data-feather="trash-2" class="me-1"></i> Delete Selected
                            </button>
                            <div>
                                {{ $drivers->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Select all checkbox
    jQuery('#selectAll').on('change', function() {
        jQuery('.row-checkbox').prop('checked', jQuery(this).is(':checked'));
        updateBulkDeleteBtn();
    });

    // Individual checkboxes
    jQuery('.row-checkbox').on('change', function() {
        updateBulkDeleteBtn();
        if (!jQuery(this).is(':checked')) {
            jQuery('#selectAll').prop('checked', false);
        } else if (jQuery('.row-checkbox:checked').length === jQuery('.row-checkbox').length) {
            jQuery('#selectAll').prop('checked', true);
        }
    });

    function updateBulkDeleteBtn() {
        var checkedCount = jQuery('.row-checkbox:checked').length;
        jQuery('#bulkDeleteBtn').prop('disabled', checkedCount === 0);
    }

    // Bulk delete
    jQuery('#bulkDeleteBtn').on('click', function() {
        var count = jQuery('.row-checkbox:checked').length;
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to delete ' + count + ' driver(s). This action can be undone from trash.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery('#bulkDeleteForm').submit();
            }
        });
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
