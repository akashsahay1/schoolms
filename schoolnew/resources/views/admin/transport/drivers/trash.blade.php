@extends('layouts.app')

@section('title', 'Drivers Trash')

@section('page-title', 'Transport - Drivers Trash')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.drivers.index') }}">Drivers</a></li>
    <li class="breadcrumb-item active">Trash</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold">Deleted Drivers <span class="text-muted fw-normal">({{ $drivers->total() }})</span></h6>
                <div class="d-flex gap-2 align-items-center">
                    @if($drivers->total() > 0)
                        <form action="{{ route('admin.drivers.empty-trash') }}" method="POST" id="emptyTrashForm" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger" id="emptyTrashBtn">
                                <i class="icon-trash me-1"></i> Empty Trash
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-primary">
                        <i class="icon-arrow-left me-1"></i> Back to Drivers
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
                    <i class="icon-check me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
                    <i class="icon-alert me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
                </div>
            @endif

            <!-- Bulk Actions -->
            <div class="d-flex gap-2 mb-3 d-none" id="bulkActions">
                <button type="button" class="btn btn-success" id="bulkRestoreBtn">
                    <i class="icon-reload me-1"></i> Restore Selected
                </button>
                <button type="button" class="btn btn-danger" id="bulkDeleteBtn">
                    <i class="icon-trash me-1"></i> Delete Permanently
                </button>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll" autocomplete="off">
                            </th>
                            <th style="width: 5%;">#</th>
                            <th style="width: 12%;">Employee ID</th>
                            <th style="width: 22%;">Name</th>
                            <th style="width: 14%;">Phone</th>
                            <th style="width: 14%;">License</th>
                            <th style="width: 16%;">Deleted At</th>
                            <th style="width: 12%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $driver->id }}" data-name="{{ $driver->full_name }}" autocomplete="off">
                                </td>
                                <td class="text-muted">{{ $drivers->firstItem() + $loop->index }}</td>
                                <td>
                                    <span class="badge badge-light-primary px-2">{{ $driver->employee_id }}</span>
                                </td>
                                <td>
                                    <div style="line-height: 1.3;">
                                        <span class="fw-bold">{{ $driver->full_name }}</span>
                                        @if($driver->email)
                                            <br><small class="text-muted">{{ $driver->email }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $driver->phone }}</td>
                                <td><span class="text-muted">{{ $driver->license_number }}</span></td>
                                <td>
                                    <span class="text-muted" title="{{ $driver->deleted_at->format('d M Y, h:i A') }}">
                                        {{ $driver->deleted_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="{{ route('admin.drivers.restore', $driver->id) }}" method="POST" class="d-inline restore-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restore">
                                                <i class="icon-reload" style="font-size: 14px;"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger force-delete-btn" title="Delete Permanently" data-id="{{ $driver->id }}" data-name="{{ $driver->full_name }}">
                                            <i class="icon-trash" style="font-size: 14px;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                            <i class="icon-check" style="font-size: 24px; color: #27ae60;"></i>
                                        </div>
                                        <h6 class="mb-1">Trash is Empty</h6>
                                        <p class="text-muted mb-0">No deleted drivers found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($drivers->hasPages())
            <div class="card-footer bg-white">
                {{ $drivers->links() }}
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
    jQuery('.row-checkbox').prop('checked', false);

    function updateBulkState() {
        var checkedCount = jQuery('.row-checkbox:checked').length;
        var totalCount = jQuery('.row-checkbox').length;

        if (checkedCount > 0) {
            jQuery('#bulkActions').removeClass('d-none');
        } else {
            jQuery('#bulkActions').addClass('d-none');
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

    function getSelectedData() {
        var ids = [];
        var names = [];
        jQuery('.row-checkbox:checked').each(function() {
            ids.push(jQuery(this).val());
            names.push(jQuery(this).data('name'));
        });
        return { ids: ids, names: names };
    }

    // Bulk Restore
    jQuery(document).on('click', '#bulkRestoreBtn', function() {
        var data = getSelectedData();
        if (data.ids.length === 0) return;

        var namesText = data.ids.length <= 5
            ? data.names.join(', ')
            : data.names.slice(0, 5).join(', ') + ' and ' + (data.ids.length - 5) + ' more';

        Swal.fire({
            title: 'Restore Drivers?',
            html: 'You are about to restore <strong>' + data.ids.length + '</strong> driver(s):<br><br><small>' + namesText + '</small>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, restore them!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.drivers.bulk-restore") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', ids: data.ids },
                    beforeSend: function() {
                        Swal.fire({ title: 'Restoring...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
                    },
                    success: function(response) {
                        Swal.fire({ icon: 'success', title: 'Restored!', text: response.message }).then(function() { window.location.reload(); });
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseJSON?.message || 'An error occurred.' });
                    }
                });
            }
        });
    });

    // Bulk Permanent Delete
    jQuery(document).on('click', '#bulkDeleteBtn', function() {
        var data = getSelectedData();
        if (data.ids.length === 0) return;

        var namesText = data.ids.length <= 5
            ? data.names.join(', ')
            : data.names.slice(0, 5).join(', ') + ' and ' + (data.ids.length - 5) + ' more';

        Swal.fire({
            title: 'Delete Permanently?',
            html: '<div class="text-danger"><strong>This action cannot be undone!</strong></div><br>You are about to permanently delete <strong>' + data.ids.length + '</strong> driver(s):<br><br><small>' + namesText + '</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete permanently!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.drivers.bulk-force-delete") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', ids: data.ids },
                    beforeSend: function() {
                        Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
                    },
                    success: function(response) {
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message }).then(function() { window.location.reload(); });
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseJSON?.message || 'An error occurred.' });
                    }
                });
            }
        });
    });

    // Single force delete
    jQuery(document).on('click', '.force-delete-btn', function() {
        var id = jQuery(this).data('id');
        var name = jQuery(this).data('name');
        var btn = jQuery(this);

        Swal.fire({
            title: 'Delete Permanently?',
            html: '<div class="text-danger"><strong>This cannot be undone!</strong></div><br>Permanently delete <strong>' + name + '</strong>?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.drivers.force-delete", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    beforeSend: function() {
                        Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
                    },
                    success: function() {
                        btn.closest('tr').fadeOut(300, function() {
                            jQuery(this).remove();
                        });
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: name + ' permanently deleted.' });
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseJSON?.message || 'An error occurred.' });
                    }
                });
            }
        });
    });

    // Empty trash
    jQuery(document).on('click', '#emptyTrashBtn', function() {
        Swal.fire({
            title: 'Empty Trash?',
            html: '<div class="text-danger"><strong>This will permanently delete ALL items in trash.</strong></div><br>This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, empty trash!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery('#emptyTrashForm').submit();
            }
        });
    });
});
</script>
@endpush
