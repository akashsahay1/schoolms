@extends('layouts.app')

@section('title', 'Route Assignments - Trash')

@section('page-title', 'Transport - Trash')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.assignments.index') }}">Route Assignments</a></li>
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
                    <h5>Trashed Assignments</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success d-none" id="bulkRestoreBtn">
                            <i data-feather="refresh-cw" class="me-1"></i> Restore Selected (<span id="selectedRestoreCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-danger d-none" id="bulkForceDeleteBtn">
                            <i data-feather="trash-2" class="me-1"></i> Delete Permanently (<span id="selectedDeleteCount">0</span>)
                        </button>
                        @if($assignments->count() > 0)
                            <form action="{{ route('admin.transport.assignments.empty-trash') }}" method="POST" class="d-inline" id="emptyTrashForm">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline-danger" id="emptyTrashBtn">
                                    <i data-feather="trash" class="me-1"></i> Empty Trash
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.transport.assignments.index') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Back to List
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
                                <th>Student</th>
                                <th>Class/Section</th>
                                <th>Route</th>
                                <th>Deleted At</th>
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
                                        @if($assignment->student && $assignment->student->section)
                                            <span class="text-muted">/ {{ $assignment->student->section->name }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $assignment->route->route_name ?? '-' }}</td>
                                    <td>{{ $assignment->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.transport.assignments.restore', $assignment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="Restore">
                                                    <i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.transport.assignments.force-delete', $assignment->id) }}" method="POST" class="d-inline force-delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm force-delete-btn" title="Delete Permanently" data-name="{{ $assignment->student->first_name ?? 'N/A' }} {{ $assignment->student->last_name ?? '' }}">
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
                                        <a href="{{ route('admin.transport.assignments.index') }}" class="btn btn-primary">Back to Assignments</a>
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
    const bulkRestoreBtn = jQuery('#bulkRestoreBtn');
    const bulkForceDeleteBtn = jQuery('#bulkForceDeleteBtn');
    const selectedRestoreCount = jQuery('#selectedRestoreCount');
    const selectedDeleteCount = jQuery('#selectedDeleteCount');

    function updateBulkState() {
        const checkedCount = jQuery('.assignment-checkbox:checked').length;
        selectedRestoreCount.text(checkedCount);
        selectedDeleteCount.text(checkedCount);

        if (checkedCount > 0) {
            bulkRestoreBtn.removeClass('d-none');
            bulkForceDeleteBtn.removeClass('d-none');
        } else {
            bulkRestoreBtn.addClass('d-none');
            bulkForceDeleteBtn.addClass('d-none');
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
        updateBulkState();
    });

    assignmentCheckboxes.on('change', function() {
        updateBulkState();
    });

    // Bulk Restore
    bulkRestoreBtn.on('click', function() {
        const selectedIds = [];
        jQuery('.assignment-checkbox:checked').each(function() {
            selectedIds.push(jQuery(this).val());
        });

        Swal.fire({
            title: 'Restore Assignments?',
            text: `You are about to restore ${selectedIds.length} assignment(s).`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, restore them'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.transport.assignments.bulk-restore") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', ids: selectedIds },
                    success: function(response) {
                        Swal.fire('Restored!', response.message, 'success').then(() => window.location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred.', 'error');
                    }
                });
            }
        });
    });

    // Bulk Force Delete
    bulkForceDeleteBtn.on('click', function() {
        const selectedIds = [];
        jQuery('.assignment-checkbox:checked').each(function() {
            selectedIds.push(jQuery(this).val());
        });

        Swal.fire({
            title: 'Delete Permanently?',
            html: `You are about to permanently delete <strong>${selectedIds.length}</strong> assignment(s).<br><br><strong class="text-danger">This action cannot be undone!</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete permanently'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.transport.assignments.bulk-force-delete") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', ids: selectedIds },
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

    // Single Force Delete
    jQuery(document).on('click', '.force-delete-btn', function(e) {
        e.preventDefault();
        var form = jQuery(this).closest('form');
        var itemName = jQuery(this).data('name') || 'this assignment';

        Swal.fire({
            title: 'Delete Permanently?',
            html: `You are about to permanently delete assignment for <strong>${itemName}</strong>.<br><br><strong class="text-danger">This action cannot be undone!</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete permanently'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Empty Trash
    jQuery('#emptyTrashBtn').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Empty Trash?',
            html: `You are about to permanently delete all items in trash.<br><br><strong class="text-danger">This action cannot be undone!</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, empty trash'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery('#emptyTrashForm').submit();
            }
        });
    });

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
