@extends('layouts.app')

@section('title', 'Book Categories Trash')

@section('page-title', 'Book Categories Trash')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.library.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">Trash</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Success/Error Messages -->
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
                    <h5>Deleted Categories ({{ $trashedCount }})</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success d-none" id="bulkRestoreBtn">
                            <i data-feather="rotate-ccw" class="me-1"></i> Restore Selected (<span id="restoreCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn">
                            <i data-feather="trash-2" class="me-1"></i> Delete Permanently (<span id="deleteCount">0</span>)
                        </button>
                        @if($trashedCount > 0)
                            <button type="button" class="btn btn-outline-danger" id="emptyTrashBtn">
                                <i data-feather="trash" class="me-1"></i> Empty Trash
                            </button>
                        @endif
                        <a href="{{ route('admin.library.categories.index') }}" class="btn btn-outline-primary">
                            <i data-feather="arrow-left" class="me-1"></i> Back to Categories
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search -->
                <form method="GET" action="{{ route('admin.library.categories.trash') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by name or description..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i data-feather="search" class="me-1"></i> Search
                            </button>
                        </div>
                        @if(request('search'))
                            <div class="col-md-1">
                                <a href="{{ route('admin.library.categories.trash') }}" class="btn btn-outline-secondary w-100" title="Clear">
                                    <i data-feather="x"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </form>

                <!-- Categories Table -->
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
                                <th>Deleted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input category-checkbox" value="{{ $category->id }}" data-name="{{ $category->name }}">
                                    </td>
                                    <td>{{ $categories->firstItem() + $loop->index }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ Str::limit($category->description, 50) ?? '-' }}</td>
                                    <td>
                                        <span class="text-muted" title="{{ $category->deleted_at->format('d M Y, h:i A') }}">
                                            {{ $category->deleted_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            <form action="{{ route('admin.library.categories.restore', $category->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="square-white border-0 bg-transparent p-0" title="Restore">
                                                    <i class="fa-solid fa-rotate-left text-success"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="square-white border-0 bg-transparent p-0 force-delete-btn" title="Delete Permanently" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                                <svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="trash-2" style="width: 48px; height: 48px;"></i>
                                            <p class="mt-2 mb-0">Trash is empty.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Empty Trash Form (hidden) -->
<form id="emptyTrashForm" action="{{ route('admin.library.categories.empty-trash') }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    const selectAllCheckbox = jQuery('#selectAll');
    const categoryCheckboxes = jQuery('.category-checkbox');
    const bulkRestoreBtn = jQuery('#bulkRestoreBtn');
    const bulkDeleteBtn = jQuery('#bulkDeleteBtn');
    const restoreCountSpan = jQuery('#restoreCount');
    const deleteCountSpan = jQuery('#deleteCount');

    // Update selected count and toggle bulk buttons
    function updateBulkState() {
        const checkedCount = jQuery('.category-checkbox:checked').length;
        restoreCountSpan.text(checkedCount);
        deleteCountSpan.text(checkedCount);

        if (checkedCount > 0) {
            bulkRestoreBtn.removeClass('d-none');
            bulkDeleteBtn.removeClass('d-none');
        } else {
            bulkRestoreBtn.addClass('d-none');
            bulkDeleteBtn.addClass('d-none');
        }

        // Update select all checkbox state
        const totalCheckboxes = categoryCheckboxes.length;
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
        categoryCheckboxes.prop('checked', jQuery(this).is(':checked'));
        updateBulkState();
    });

    // Individual checkbox handler
    categoryCheckboxes.on('change', function() {
        updateBulkState();
    });

    // Get selected IDs and names
    function getSelectedData() {
        const selectedIds = [];
        const selectedNames = [];

        jQuery('.category-checkbox:checked').each(function() {
            selectedIds.push(jQuery(this).val());
            selectedNames.push(jQuery(this).data('name'));
        });

        return { ids: selectedIds, names: selectedNames };
    }

    // Bulk Restore button handler
    bulkRestoreBtn.on('click', function() {
        const { ids, names } = getSelectedData();

        if (ids.length === 0) return;

        const namesText = ids.length <= 5
            ? names.join(', ')
            : names.slice(0, 5).join(', ') + ' and ' + (ids.length - 5) + ' more';

        Swal.fire({
            title: 'Restore Categories?',
            html: `You are about to restore <strong>${ids.length}</strong> category(ies):<br><br><small>${namesText}</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, restore them!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.library.categories.bulk-restore") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        category_ids: ids
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Restoring...',
                            text: 'Please wait while we restore the selected categories.',
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
                            title: 'Restored!',
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

    // Bulk Permanent Delete button handler
    bulkDeleteBtn.on('click', function() {
        const { ids, names } = getSelectedData();

        if (ids.length === 0) return;

        const namesText = ids.length <= 5
            ? names.join(', ')
            : names.slice(0, 5).join(', ') + ' and ' + (ids.length - 5) + ' more';

        Swal.fire({
            title: 'Permanently Delete?',
            html: `<div class="text-danger"><strong>Warning: This action cannot be undone!</strong></div><br>You are about to permanently delete <strong>${ids.length}</strong> category(ies):<br><br><small>${namesText}</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete permanently!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.library.categories.bulk-force-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        category_ids: ids
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait while we permanently delete the selected categories.',
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
                            title: 'Deleted!',
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

    // Individual force delete button handler
    jQuery('.force-delete-btn').on('click', function() {
        const id = jQuery(this).data('id');
        const name = jQuery(this).data('name');

        Swal.fire({
            title: 'Permanently Delete?',
            html: `<div class="text-danger"><strong>Warning: This action cannot be undone!</strong></div><br>You are about to permanently delete <strong>${name}</strong>.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete permanently!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.library.categories.force-delete", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Deleting...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function() {
                        window.location.reload();
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

    // Empty Trash button handler
    jQuery('#emptyTrashBtn').on('click', function() {
        Swal.fire({
            title: 'Empty Trash?',
            html: `<div class="text-danger"><strong>Warning: This action cannot be undone!</strong></div><br>You are about to permanently delete <strong>all {{ $trashedCount }} category(ies)</strong> in the trash.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, empty trash!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery('#emptyTrashForm').submit();
            }
        });
    });

    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
