@extends('layouts.app')

@section('title', 'Add Custom Field')

@section('page-title', 'Add Custom Field')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.custom-fields.index') }}">Custom Fields</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Add New Custom Field</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.custom-fields.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Aadhaar Number" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field Type <span class="text-danger">*</span></label>
                            <select name="field_type" id="field_type" class="form-select @error('field_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="text" {{ old('field_type') == 'text' ? 'selected' : '' }}>Text</option>
                                <option value="textarea" {{ old('field_type') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                <option value="number" {{ old('field_type') == 'number' ? 'selected' : '' }}>Number</option>
                                <option value="date" {{ old('field_type') == 'date' ? 'selected' : '' }}>Date</option>
                                <option value="select" {{ old('field_type') == 'select' ? 'selected' : '' }}>Dropdown (Select)</option>
                                <option value="checkbox" {{ old('field_type') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                <option value="radio" {{ old('field_type') == 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                                <option value="file" {{ old('field_type') == 'file' ? 'selected' : '' }}>File Upload</option>
                            </select>
                            @error('field_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applies To <span class="text-danger">*</span></label>
                            <select name="applies_to" class="form-select @error('applies_to') is-invalid @enderror" required>
                                <option value="student" {{ old('applies_to') == 'student' ? 'selected' : '' }}>Student Only</option>
                                <option value="teacher" {{ old('applies_to') == 'teacher' ? 'selected' : '' }}>Teacher Only</option>
                                <option value="all" {{ old('applies_to', 'all') == 'all' ? 'selected' : '' }}>Both (Student & Teacher)</option>
                            </select>
                            @error('applies_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                    </div>

                    <!-- Options Section (for select, radio, checkbox) -->
                    <div class="mb-3" id="optionsSection" style="display: none;">
                        <label class="form-label">Options <span class="text-danger">*</span></label>
                        @error('options')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror
                        <div id="optionsList">
                            @if(old('options'))
                                @foreach(old('options') as $i => $option)
                                    <div class="input-group mb-2 option-row">
                                        <input type="text" name="options[]" class="form-control" value="{{ $option }}" placeholder="Option {{ $i + 1 }}">
                                        <button type="button" class="btn btn-outline-danger remove-option"><i data-feather="x"></i></button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 option-row">
                                    <input type="text" name="options[]" class="form-control" placeholder="Option 1">
                                    <button type="button" class="btn btn-outline-danger remove-option"><i data-feather="x"></i></button>
                                </div>
                                <div class="input-group mb-2 option-row">
                                    <input type="text" name="options[]" class="form-control" placeholder="Option 2">
                                    <button type="button" class="btn btn-outline-danger remove-option"><i data-feather="x"></i></button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="addOption">
                            <i data-feather="plus" class="me-1"></i> Add Option
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_required" id="is_required" class="form-check-input" value="1" {{ old('is_required') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">Required Field</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Save Custom Field
                        </button>
                        <a href="{{ route('admin.custom-fields.index') }}" class="btn btn-secondary">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Instructions</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i> Custom fields appear in student/staff forms</li>
                    <li class="mb-2"><i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i> Select, Radio and Checkbox need options</li>
                    <li class="mb-2"><i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i> Use sort order to control field position</li>
                    <li><i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i> Inactive fields are hidden from forms</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Field Types</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-1"><strong>Text</strong> - Short single-line input</li>
                    <li class="mb-1"><strong>Textarea</strong> - Multi-line text</li>
                    <li class="mb-1"><strong>Number</strong> - Numeric input</li>
                    <li class="mb-1"><strong>Date</strong> - Date picker</li>
                    <li class="mb-1"><strong>Dropdown</strong> - Select from options</li>
                    <li class="mb-1"><strong>Checkbox</strong> - Yes/No toggle</li>
                    <li class="mb-1"><strong>Radio</strong> - Choose one option</li>
                    <li class="mb-1"><strong>File</strong> - File upload (max 2MB)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    var optionTypes = ['select', 'radio', 'checkbox'];

    function toggleOptions() {
        var fieldType = jQuery('#field_type').val();
        if (optionTypes.indexOf(fieldType) !== -1) {
            jQuery('#optionsSection').slideDown();
            jQuery('#optionsSection input[name="options[]"]').prop('disabled', false);
        } else {
            jQuery('#optionsSection').slideUp();
            jQuery('#optionsSection input[name="options[]"]').prop('disabled', true);
        }
    }

    jQuery('#field_type').on('change', toggleOptions);
    toggleOptions();

    jQuery('#addOption').on('click', function() {
        var count = jQuery('.option-row').length + 1;
        var html = '<div class="input-group mb-2 option-row">' +
            '<input type="text" name="options[]" class="form-control" placeholder="Option ' + count + '">' +
            '<button type="button" class="btn btn-outline-danger remove-option"><i data-feather="x"></i></button>' +
            '</div>';
        jQuery('#optionsList').append(html);
        if (typeof feather !== 'undefined') feather.replace();
    });

    jQuery(document).on('click', '.remove-option', function() {
        if (jQuery('.option-row').length > 1) {
            jQuery(this).closest('.option-row').remove();
        }
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
