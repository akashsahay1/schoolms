@extends('layouts.app')

@section('title', 'Leave Types - Trash')

@section('page-title', 'Staff Leaves - Trash')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.staff-leaves.types.index') }}">Leave Types</a></li>
    <li class="breadcrumb-item active">Trash</li>
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
                    <h5>Trashed Leave Types</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success d-none" id="bulkRestoreBtn">
                            <i data-feather="refresh-cw" class="me-1"></i> Restore (<span id="selectedRestoreCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-danger d-none" id="bulkForceDeleteBtn">
                            <i data-feather="trash-2" class="me-1"></i> Delete (<span id="selectedDeleteCount">0</span>)
                        </button>
                        @if($leaveTypes->count() > 0)
                            <form action="{{ route('admin.staff-leaves.types.empty-trash') }}" method="POST" class="d-inline" id="emptyTrashForm">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline-danger" id="emptyTrashBtn">
                                    <i data-feather="trash" class="me-1"></i> Empty Trash
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.staff-leaves.types.index') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Back
                        </a>
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
                                <th>Code</th>
                                <th>Days/Year</th>
                                <th>Deleted At</th>
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
                                    <td>{{ $type->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.staff-leaves.types.restore', $type->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="Restore">
                                                    <i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.staff-leaves.types.force-delete', $type->id) }}" method="POST" class="d-inline force-delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm force-delete-btn" data-name="{{ $type->name }}">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-2">Trash is empty.</p>
                                        <a href="{{ route('admin.staff-leaves.types.index') }}" class="btn btn-primary">Back to Leave Types</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($leaveTypes->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $leaveTypes->links() }}
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
    const bulkRestoreBtn = jQuery('#bulkRestoreBtn');
    const bulkForceDeleteBtn = jQuery('#bulkForceDeleteBtn');

    function updateBulkState() {
        const checkedCount = jQuery('.type-checkbox:checked').length;
        jQuery('#selectedRestoreCount, #selectedDeleteCount').text(checkedCount);

        if (checkedCount > 0) {
            bulkRestoreBtn.removeClass('d-none');
            bulkForceDeleteBtn.removeClass('d-none');
        } else {
            bulkRestoreBtn.addClass('d-none');
            bulkForceDeleteBtn.addClass('d-none');
        }
    }

    selectAllCheckbox.on('change', function() {
        typeCheckboxes.prop('checked', jQuery(this).is(':checked'));
        updateBulkState();
    });

    typeCheckboxes.on('change', updateBulkState);

    bulkRestoreBtn.on('click', function() {
        const ids = [];
        jQuery('.type-checkbox:checked').each(function() { ids.push(jQuery(this).val()); });

        jQuery.ajax({
            url: '{{ route("admin.staff-leaves.types.bulk-restore") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', ids: ids },
            success: function(response) {
                Swal.fire('Restored!', response.message, 'success').then(() => window.location.reload());
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred.', 'error');
            }
        });
    });

    bulkForceDeleteBtn.on('click', function() {
        const ids = [];
        jQuery('.type-checkbox:checked').each(function() { ids.push(jQuery(this).val()); });

        Swal.fire({
            title: 'Delete Permanently?',
            html: '<strong class="text-danger">This action cannot be undone!</strong>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete permanently'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.staff-leaves.types.bulk-force-delete") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', ids: ids },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => window.location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred.', 'error');
                    }
                });
            }
        });
    });

    jQuery(document).on('click', '.force-delete-btn', function(e) {
        e.preventDefault();
        var form = jQuery(this).closest('form');
        var name = jQuery(this).data('name');

        Swal.fire({
            title: 'Delete Permanently?',
            html: `Delete <strong>${name}</strong> permanently?<br><strong class="text-danger">This cannot be undone!</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Delete'
        }).then(function(result) {
            if (result.isConfirmed) form.submit();
        });
    });

    jQuery('#emptyTrashBtn').on('click', function() {
        Swal.fire({
            title: 'Empty Trash?',
            html: '<strong class="text-danger">This will permanently delete all items!</strong>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Empty Trash'
        }).then(function(result) {
            if (result.isConfirmed) jQuery('#emptyTrashForm').submit();
        });
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
