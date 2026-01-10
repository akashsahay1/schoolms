@extends('layouts.app')

@section('title', 'Designations')

@section('page-title', 'Designations')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Designations</li>
@endsection

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

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>All Designations</h5>
                    <div class="d-flex gap-2">
                        @can('delete staff')
                            <button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
                                <i data-feather="trash-2" class="me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
                            </button>
                            <a href="{{ route('admin.designations.trash') }}" class="btn btn-outline-danger position-relative">
                                <i data-feather="trash" class="me-1"></i> Trash
                                @if($trashedCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $trashedCount > 99 ? '99+' : $trashedCount }}
                                    </span>
                                @endif
                            </a>
                        @endcan
                        @can('create staff')
                            <a href="{{ route('admin.designations.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-1"></i> Add Designation
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll" title="Select All">
                                </th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Staff Count</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($designations as $designation)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input designation-checkbox" value="{{ $designation->id }}" data-name="{{ $designation->name }}">
                                    </td>
                                    <td>{{ $designations->firstItem() + $loop->index }}</td>
                                    <td><strong>{{ $designation->name }}</strong></td>
                                    <td>{{ Str::limit($designation->description, 50) ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-light-info">{{ $designation->staff_count }} staff</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $designation->is_active ? 'success' : 'danger' }}">
                                            {{ $designation->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            @can('edit staff')
                                                <a class="square-white" href="{{ route('admin.designations.edit', $designation) }}" title="Edit">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
                                                </a>
                                            @endcan
                                            @can('delete staff')
                                                <form action="{{ route('admin.designations.destroy', $designation) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $designation->name }}">
                                                        <svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="award" style="width: 48px; height: 48px;"></i>
                                            <p class="mt-2 mb-0">No designations found.</p>
                                            @can('create staff')
                                                <a href="{{ route('admin.designations.create') }}" class="btn btn-primary mt-3">Add First Designation</a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $designations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    const selectAllCheckbox = jQuery('#selectAll');
    const designationCheckboxes = jQuery('.designation-checkbox');
    const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
    const selectedCountSpan = jQuery('#selectedCount');

    // Update selected count and toggle bulk delete button
    function updateBulkDeleteState() {
        const checkedCount = jQuery('.designation-checkbox:checked').length;
        selectedCountSpan.text(checkedCount);

        if (checkedCount > 0) {
            bulkDeleteBtn.removeClass('d-none');
        } else {
            bulkDeleteBtn.addClass('d-none');
        }

        // Update select all checkbox state
        const totalCheckboxes = designationCheckboxes.length;
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

    // Select All checkbox handler
    selectAllCheckbox.on('change', function() {
        designationCheckboxes.prop('checked', jQuery(this).is(':checked'));
        updateBulkDeleteState();
    });

    // Individual checkbox handler
    designationCheckboxes.on('change', function() {
        updateBulkDeleteState();
    });

    // Bulk Delete button handler
    bulkDeleteBtn.on('click', function() {
        const selectedIds = [];
        const selectedNames = [];

        jQuery('.designation-checkbox:checked').each(function() {
            selectedIds.push(jQuery(this).val());
            selectedNames.push(jQuery(this).data('name'));
        });

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select at least one designation to delete.'
            });
            return;
        }

        const namesText = selectedIds.length <= 5
            ? selectedNames.join(', ')
            : selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

        Swal.fire({
            title: 'Move to Trash?',
            html: `You are about to move <strong>${selectedIds.length}</strong> designation(s) to trash:<br><br><small>${namesText}</small><br><br><small class="text-muted">You can restore them later from the trash.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, move to trash',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.designations.bulk-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        designation_ids: selectedIds
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Moving to Trash...',
                            text: 'Please wait while we move the selected designations to trash.',
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

    // Move to Trash handler (single delete)
    jQuery(document).on('click', '.move-to-trash', function(e) {
        e.preventDefault();
        var form = jQuery(this).closest('form');
        var itemName = jQuery(this).data('name') || 'this designation';

        Swal.fire({
            title: 'Move to Trash?',
            html: `You are about to move <strong>${itemName}</strong> to trash.<br><small class="text-muted">You can restore this designation later from the trash.</small>`,
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

    // Re-initialize feather icons if needed
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
