@extends('layouts.app')

@section('title', 'Leave Types')

@section('page-title', 'Staff Leaves - Leave Types')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Staff Leaves</li>
    <li class="breadcrumb-item active">Leave Types</li>
@endsection

@section('content')
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
                    <h5>Leave Types</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
                            <i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
                        </button>
                        <a href="{{ route('admin.staff-leaves.types.trash') }}" class="btn btn-outline-danger position-relative">
                            <i data-feather="trash" class="me-1"></i> Trash
                            @if($trashedCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $trashedCount > 99 ? '99+' : $trashedCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.staff-leaves.types.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Add Leave Type
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.staff-leaves.types.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="applicable_to" class="form-select">
                                <option value="">All Types</option>
                                <option value="all" {{ request('applicable_to') == 'all' ? 'selected' : '' }}>Both Staff & Students</option>
                                <option value="staff" {{ request('applicable_to') == 'staff' ? 'selected' : '' }}>Staff Only</option>
                                <option value="students" {{ request('applicable_to') == 'students' ? 'selected' : '' }}>Students Only</option>
                            </select>
                        </div>
                        <div class="col-md-2">
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
                                <th>Name</th>
                                <th>Code</th>
                                <th>Days/Year</th>
                                <th>Applicable To</th>
                                <th>Paid</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveTypes as $type)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input type-checkbox" value="{{ $type->id }}" data-name="{{ $type->name }}">
                                    </td>
                                    <td>{{ $leaveTypes->firstItem() + $loop->index }}</td>
                                    <td><strong>{{ $type->name }}</strong></td>
                                    <td><span class="badge badge-light-secondary">{{ $type->code }}</span></td>
                                    <td>{{ $type->allowed_days }}</td>
                                    <td>
                                        @if($type->applicable_to == 'all')
                                            <span class="badge badge-light-primary">All</span>
                                        @elseif($type->applicable_to == 'staff')
                                            <span class="badge badge-light-info">Staff Only</span>
                                        @else
                                            <span class="badge badge-light-warning">Students Only</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($type->is_paid)
                                            <span class="badge badge-light-success">Paid</span>
                                        @else
                                            <span class="badge badge-light-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $type->is_active ? 'success' : 'danger' }}">
                                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            <a class="square-white" href="{{ route('admin.staff-leaves.types.edit', $type) }}" title="Edit">
                                                <svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
                                            </a>
                                            <form action="{{ route('admin.staff-leaves.types.destroy', $type) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $type->name }}">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <p class="text-muted mb-2">No leave types found.</p>
                                        <a href="{{ route('admin.staff-leaves.types.create') }}" class="btn btn-primary">Add First Leave Type</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($leaveTypes->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $leaveTypes->withQueryString()->links() }}
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
    const typeCheckboxes = jQuery('.type-checkbox');
    const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
    const selectedCountSpan = jQuery('#selectedCount');

    function updateBulkDeleteState() {
        const checkedCount = jQuery('.type-checkbox:checked').length;
        selectedCountSpan.text(checkedCount);

        if (checkedCount > 0) {
            bulkDeleteBtn.removeClass('d-none');
        } else {
            bulkDeleteBtn.addClass('d-none');
        }

        const totalCheckboxes = typeCheckboxes.length;
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
        typeCheckboxes.prop('checked', jQuery(this).is(':checked'));
        updateBulkDeleteState();
    });

    typeCheckboxes.on('change', function() {
        updateBulkDeleteState();
    });

    bulkDeleteBtn.on('click', function() {
        const selectedIds = [];
        const selectedNames = [];

        jQuery('.type-checkbox:checked').each(function() {
            selectedIds.push(jQuery(this).val());
            selectedNames.push(jQuery(this).data('name'));
        });

        if (selectedIds.length === 0) {
            Swal.fire({ icon: 'warning', title: 'No Selection', text: 'Please select at least one leave type.' });
            return;
        }

        Swal.fire({
            title: 'Move to Trash?',
            html: `You are about to move <strong>${selectedIds.length}</strong> leave type(s) to trash.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, move to trash'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.staff-leaves.types.bulk-delete") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', ids: selectedIds },
                    success: function(response) {
                        Swal.fire('Moved!', response.message, 'success').then(() => window.location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred.', 'error');
                    }
                });
            }
        });
    });

    jQuery(document).on('click', '.move-to-trash', function(e) {
        e.preventDefault();
        var form = jQuery(this).closest('form');
        var itemName = jQuery(this).data('name');

        Swal.fire({
            title: 'Move to Trash?',
            html: `Move <strong>${itemName}</strong> to trash?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FC4438',
            confirmButtonText: 'Yes, move to trash',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
