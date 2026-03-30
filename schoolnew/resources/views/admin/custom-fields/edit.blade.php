@extends('layouts.app')

@section('title', 'Edit Custom Field')

@section('page-title', 'Edit Custom Field')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.custom-fields.index') }}">Custom Fields</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Edit Custom Field</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.custom-fields.update', $customField) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customField->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field Type <span class="text-danger">*</span></label>
                            <select name="field_type" id="field_type" class="form-select @error('field_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="text" {{ old('field_type', $customField->field_type) == 'text' ? 'selected' : '' }}>Text</option>
                                <option value="textarea" {{ old('field_type', $customField->field_type) == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                <option value="number" {{ old('field_type', $customField->field_type) == 'number' ? 'selected' : '' }}>Number</option>
                                <option value="date" {{ old('field_type', $customField->field_type) == 'date' ? 'selected' : '' }}>Date</option>
                                <option value="select" {{ old('field_type', $customField->field_type) == 'select' ? 'selected' : '' }}>Dropdown (Select)</option>
                                <option value="checkbox" {{ old('field_type', $customField->field_type) == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                <option value="radio" {{ old('field_type', $customField->field_type) == 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                                <option value="file" {{ old('field_type', $customField->field_type) == 'file' ? 'selected' : '' }}>File Upload</option>
                            </select>
                            @error('field_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applies To <span class="text-danger">*</span></label>
                            <select name="applies_to" id="applies_to" class="form-select @error('applies_to') is-invalid @enderror" required>
                                <option value="student" {{ old('applies_to', $customField->applies_to) == 'student' ? 'selected' : '' }}>Student Only</option>
                                <option value="teacher" {{ old('applies_to', $customField->applies_to) == 'teacher' ? 'selected' : '' }}>Teacher Only</option>
                                <option value="all" {{ old('applies_to', $customField->applies_to) == 'all' ? 'selected' : '' }}>Both (Student & Teacher)</option>
                            </select>
                            @error('applies_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Place in Section <span class="text-danger">*</span></label>
                            <select name="section" id="section_select" class="form-select @error('section') is-invalid @enderror">
                                <!-- Populated by JS -->
                            </select>
                            @error('section')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Choose which form section this field appears in</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $customField->sort_order) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Lower numbers appear first within the section</small>
                        </div>
                    </div>

                    <!-- Options Section -->
                    <div class="mb-3" id="optionsSection" style="display: none;">
                        <label class="form-label">Options <span class="text-danger">*</span></label>
                        @error('options')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror
                        <div id="optionsList">
                            @php
                                $options = old('options', $customField->options ?? []);
                            @endphp
                            @if(!empty($options))
                                @foreach($options as $i => $option)
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
                                <input type="checkbox" name="is_required" id="is_required" class="form-check-input" value="1" {{ old('is_required', $customField->is_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">Required Field</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $customField->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Custom Field
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
                <h5>Field Info</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Created</span>
                    <span>{{ $customField->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Updated</span>
                    <span>{{ $customField->updated_at->format('d M Y, h:i A') }}</span>
                </div>
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
    // Section options per scope
    var sectionOptions = {!! json_encode($sections) !!};
    var oldSection = '{{ old('section', $customField->section ?? 'additional_information') }}';

    function updateSections() {
        var appliesTo = jQuery('#applies_to').val();
        var select = jQuery('#section_select');
        select.empty();

        var scope = (appliesTo === 'all') ? 'student' : appliesTo;
        var options = sectionOptions[scope] || {};

        jQuery.each(options, function(key, label) {
            var option = jQuery('<option>').val(key).text(label);
            if (key === oldSection) option.prop('selected', true);
            select.append(option);
        });
    }

    jQuery('#applies_to').on('change', updateSections);
    updateSections();

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
