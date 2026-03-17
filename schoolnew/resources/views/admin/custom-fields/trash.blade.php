@extends('layouts.app')

@section('title', 'Custom Fields - Trash')

@section('page-title', 'Custom Fields - Trash')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.custom-fields.index') }}">Custom Fields</a></li>
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
                    <h5>Trashed Custom Fields</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success d-none" id="bulkRestoreBtn">
                            <i data-feather="refresh-cw" class="me-1"></i> Restore (<span id="selectedRestoreCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-danger d-none" id="bulkForceDeleteBtn">
                            <i data-feather="trash-2" class="me-1"></i> Delete (<span id="selectedDeleteCount">0</span>)
                        </button>
                        <a href="{{ route('admin.custom-fields.index') }}" class="btn btn-secondary">
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
                                <th>Field Type</th>
                                <th>Applies To</th>
                                <th>Deleted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customFields as $field)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input item-checkbox" value="{{ $field->id }}" data-name="{{ $field->name }}">
                                    </td>
                                    <td>{{ $customFields->firstItem() + $loop->index }}</td>
                                    <td><strong>{{ $field->name }}</strong></td>
                                    <td><span class="badge badge-light-info">{{ ucfirst($field->field_type) }}</span></td>
                                    <td>
                                        @if($field->applies_to == 'all')
                                            <span class="badge badge-light-primary">Both</span>
                                        @elseif($field->applies_to == 'student')
                                            <span class="badge badge-light-warning">Student</span>
                                        @elseif($field->applies_to == 'teacher')
                                            <span class="badge badge-light-info">Teacher</span>
                                        @else
                                            <span class="badge badge-light-secondary">Staff</span>
                                        @endif
                                    </td>
                                    <td>{{ $field->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.custom-fields.restore', $field->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Restore">
                                                    <i class="icon-reload"></i> Restore
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.custom-fields.force-delete', $field->id) }}" method="POST" class="d-inline force-delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger force-delete-btn" data-name="{{ $field->name }}">
                                                    <i class="icon-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-2">Trash is empty.</p>
                                        <a href="{{ route('admin.custom-fields.index') }}" class="btn btn-primary">Back to Custom Fields</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customFields->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $customFields->links() }}
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
    var selectAllCheckbox = jQuery('#selectAll');
    var itemCheckboxes = jQuery('.item-checkbox');
    var bulkRestoreBtn = jQuery('#bulkRestoreBtn');
    var bulkForceDeleteBtn = jQuery('#bulkForceDeleteBtn');

    function updateBulkState() {
        var checkedCount = jQuery('.item-checkbox:checked').length;
        jQuery('#selectedRestoreCount, #selectedDeleteCount').text(checkedCount);

        if (checkedCount > 0) {
            bulkRestoreBtn.removeClass('d-none');
            bulkForceDeleteBtn.removeClass('d-none');
        } else {
            bulkRestoreBtn.addClass('d-none');
            bulkForceDeleteBtn.addClass('d-none');
        }

        var totalCheckboxes = itemCheckboxes.length;
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
        itemCheckboxes.prop('checked', jQuery(this).is(':checked'));
        updateBulkState();
    });

    itemCheckboxes.on('change', function() {
        updateBulkState();
    });

    // Bulk restore
    bulkRestoreBtn.on('click', function() {
        var ids = [];
        jQuery('.item-checkbox:checked').each(function() { ids.push(jQuery(this).val()); });

        if (ids.length === 0) return;

        Swal.fire({
            title: 'Restore Selected?',
            html: 'Restore <strong>' + ids.length + '</strong> custom field(s)?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Yes, restore'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.custom-fields.bulk-restore") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', ids: ids },
                    success: function(response) {
                        Swal.fire('Restored!', response.message, 'success').then(function() { window.location.reload(); });
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred.', 'error');
                    }
                });
            }
        });
    });

    // Bulk force delete
    bulkForceDeleteBtn.on('click', function() {
        var ids = [];
        jQuery('.item-checkbox:checked').each(function() { ids.push(jQuery(this).val()); });

        if (ids.length === 0) return;

        Swal.fire({
            title: 'Delete Permanently?',
            html: 'Delete <strong>' + ids.length + '</strong> custom field(s) permanently?<br><strong class="text-danger">This cannot be undone!</strong>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete permanently'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.custom-fields.bulk-force-delete") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', ids: ids },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success').then(function() { window.location.reload(); });
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred.', 'error');
                    }
                });
            }
        });
    });

    // Individual force delete
    jQuery(document).on('click', '.force-delete-btn', function(e) {
        e.preventDefault();
        var form = jQuery(this).closest('form');
        var name = jQuery(this).data('name');

        Swal.fire({
            title: 'Delete Permanently?',
            html: 'Delete <strong>' + name + '</strong> permanently?<br><strong class="text-danger">This cannot be undone!</strong>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Delete',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) form.submit();
        });
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
