@extends('layouts.app')

@section('title', 'Custom Fields')

@section('page-title', 'Custom Fields')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Custom Fields</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
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

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Custom Fields</h5>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn" style="color: #fff;">
                            <i class="icon-trash me-1" style="color: #fff;"></i> Delete (<span id="selectedCount" style="color: #fff;">0</span>)
                        </button>
                        <a href="{{ route('admin.custom-fields.form-settings') }}" class="btn btn-outline-primary">
                            <i class="icon-settings me-1"></i> Form Settings
                        </a>
                        <a href="{{ route('admin.custom-fields.trash') }}" class="btn btn-outline-danger position-relative">
                            <i class="icon-trash me-1"></i> Trash
                            @if($trashedCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $trashedCount > 99 ? '99+' : $trashedCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.custom-fields.create') }}" class="btn btn-primary">
                            <i class="icon-plus me-1"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.custom-fields.index') }}" method="GET" class="row g-3 align-items-end mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Applies To</label>
                        <select name="applies_to" class="form-select">
                            <option value="">All Types</option>
                            <option value="student" {{ request('applies_to') == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="teacher" {{ request('applies_to') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="all" {{ request('applies_to') == 'all' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Field Type</label>
                        <select name="field_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="text" {{ request('field_type') == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="textarea" {{ request('field_type') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                            <option value="number" {{ request('field_type') == 'number' ? 'selected' : '' }}>Number</option>
                            <option value="date" {{ request('field_type') == 'date' ? 'selected' : '' }}>Date</option>
                            <option value="select" {{ request('field_type') == 'select' ? 'selected' : '' }}>Select</option>
                            <option value="checkbox" {{ request('field_type') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                            <option value="radio" {{ request('field_type') == 'radio' ? 'selected' : '' }}>Radio</option>
                            <option value="file" {{ request('field_type') == 'file' ? 'selected' : '' }}>File</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="icon-filter me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'applies_to', 'field_type']))
                                <a href="{{ route('admin.custom-fields.index') }}" class="btn btn-outline-secondary" title="Reset">
                                    <i class="icon-reload"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll" title="Select All" autocomplete="off">
                                </th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Field Type</th>
                                <th>Applies To</th>
                                <th>Section</th>
                                <th>Required</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customFields as $field)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input field-checkbox" value="{{ $field->id }}" data-name="{{ $field->name }}" autocomplete="off">
                                    </td>
                                    <td>{{ $customFields->firstItem() + $loop->index }}</td>
                                    <td><strong>{{ $field->name }}</strong></td>
                                    <td><span class="badge badge-light-info">{{ ucfirst($field->field_type) }}</span></td>
                                    <td>
                                        @if($field->applies_to == 'all')
                                            <span class="badge badge-light-primary">All</span>
                                        @elseif($field->applies_to == 'student')
                                            <span class="badge badge-light-warning">Student</span>
                                        @elseif($field->applies_to == 'teacher')
                                            <span class="badge badge-light-info">Teacher</span>
                                        @else
                                            <span class="badge badge-light-secondary">Staff</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $sectionLabel = $field->section ?? 'additional_information';
                                            $scope = $field->applies_to === 'all' ? 'student' : $field->applies_to;
                                            $allSections = \App\Models\CustomField::SECTIONS[$scope] ?? [];
                                            $sectionLabel = $allSections[$field->section] ?? ucwords(str_replace('_', ' ', $field->section ?? 'Additional Information'));
                                        @endphp
                                        <small class="text-muted">{{ $sectionLabel }}</small>
                                    </td>
                                    <td>
                                        @if($field->is_required)
                                            <span class="badge badge-light-danger">Required</span>
                                        @else
                                            <span class="badge badge-light-secondary">Optional</span>
                                        @endif
                                    </td>
                                    <td>{{ $field->sort_order }}</td>
                                    <td>
                                        <span class="badge badge-light-{{ $field->is_active ? 'success' : 'danger' }}">
                                            {{ $field->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="common-align gap-2 justify-content-center">
                                            <a class="square-white" href="{{ route('admin.custom-fields.edit', $field) }}" title="Edit">
                                                <svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
                                            </a>
                                            <form action="{{ route('admin.custom-fields.destroy', $field) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 move-to-trash" title="Move to Trash" data-name="{{ $field->name }}">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                                <i class="icon-layers" style="font-size: 24px; color: #95a5a6;"></i>
                                            </div>
                                            <p class="text-muted mb-2">No custom fields found.</p>
                                            <a href="{{ route('admin.custom-fields.create') }}" class="btn btn-primary btn-sm">Add First Custom Field</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                @include('components.pagination-info', ['paginator' => $customFields])
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
    jQuery('.field-checkbox').prop('checked', false);

    function updateBulkState() {
        var checkedCount = jQuery('.field-checkbox:checked').length;
        var totalCount = jQuery('.field-checkbox').length;
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
        jQuery('.field-checkbox').prop('checked', jQuery(this).is(':checked'));
        updateBulkState();
    });

    jQuery(document).on('change', '.field-checkbox', function() {
        updateBulkState();
    });

    // Bulk Delete
    jQuery(document).on('click', '#bulkDeleteBtn', function() {
        var selectedIds = [];
        var selectedNames = [];

        jQuery('.field-checkbox:checked').each(function() {
            selectedIds.push(jQuery(this).val());
            selectedNames.push(jQuery(this).data('name'));
        });

        if (selectedIds.length === 0) return;

        var namesText = selectedIds.length <= 5
            ? selectedNames.join(', ')
            : selectedNames.slice(0, 5).join(', ') + ' and ' + (selectedIds.length - 5) + ' more';

        Swal.fire({
            title: 'Move to Trash?',
            html: 'Move <strong>' + selectedIds.length + '</strong> custom field(s) to trash:<br><br><small>' + namesText + '</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FC4438',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, move to trash',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: '{{ route("admin.custom-fields.bulk-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processing...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: function() { Swal.showLoading(); }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Done!',
                            text: response.message,
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

    // Single Delete
    jQuery(document).on('click', '.move-to-trash', function(e) {
        e.preventDefault();
        var form = jQuery(this).closest('form');
        var itemName = jQuery(this).data('name');

        Swal.fire({
            title: 'Move to Trash?',
            html: 'Move <strong>' + itemName + '</strong> to trash?',
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
