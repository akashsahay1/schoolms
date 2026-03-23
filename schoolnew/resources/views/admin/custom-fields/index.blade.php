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
                    <h5>Custom Fields</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.custom-fields.form-settings') }}" class="btn btn-outline-primary">
                            <i data-feather="sliders" class="me-1"></i> Form Settings
                        </a>
                        <a href="{{ route('admin.custom-fields.trash') }}" class="btn btn-outline-danger position-relative">
                            <i data-feather="trash" class="me-1"></i> Trash
                            @if($trashedCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $trashedCount > 99 ? '99+' : $trashedCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.custom-fields.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.custom-fields.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="applies_to" class="form-select">
                                <option value="">All Types</option>
                                <option value="student" {{ request('applies_to') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="teacher" {{ request('applies_to') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="all" {{ request('applies_to') == 'all' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="field_type" class="form-select">
                                <option value="">All Field Types</option>
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
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="icon-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if(request()->hasAny(['search', 'applies_to', 'field_type']))
                            <div class="col-md-2">
                                <a href="{{ route('admin.custom-fields.index') }}" class="btn btn-outline-secondary w-100">
                                    <i data-feather="x" class="me-1"></i> Clear
                                </a>
                            </div>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Field Type</th>
                                <th>Applies To</th>
                                <th>Required</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customFields as $field)
                                <tr>
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
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
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
                                    <td colspan="8" class="text-center py-4">
                                        <p class="text-muted mb-2">No custom fields found.</p>
                                        <a href="{{ route('admin.custom-fields.create') }}" class="btn btn-primary">Add First Custom Field</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customFields->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $customFields->withQueryString()->links() }}
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
