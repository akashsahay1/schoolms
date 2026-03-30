{{--
    Reusable custom fields partial for student/staff create/edit forms

    Variables:
    - $customFields: Collection of active CustomField models
    - $customFieldValues: Array of [custom_field_id => value] (for edit forms)
    - $formContext: 'create' or 'edit'
--}}

@php
    // Only show fields assigned to "Additional Information" section (or legacy fields with no section)
    $additionalFields = $customFields->filter(function($field) {
        return in_array($field->section ?? 'additional_information', ['additional_information', '']);
    });
@endphp

@if($additionalFields->count() > 0)
<div class="card">
    <div class="card-header">
        <h5>Additional Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($additionalFields as $field)
                @php
                    $fieldName = 'custom_fields[' . $field->id . ']';
                    $fieldId = 'custom_field_' . $field->id;
                    $currentValue = old('custom_fields.' . $field->id, $customFieldValues[$field->id] ?? '');
                @endphp

                @if($field->field_type === 'textarea')
                    <div class="col-12 mb-3">
                        <label class="form-label" for="{{ $fieldId }}">
                            {{ $field->name }}
                            @if($field->is_required) <span class="text-danger">*</span> @endif
                        </label>
                        <textarea class="form-control @error('custom_fields.' . $field->id) is-invalid @enderror" id="{{ $fieldId }}" name="{{ $fieldName }}" rows="3" {{ $field->is_required ? 'required' : '' }}>{{ $currentValue }}</textarea>
                        @error('custom_fields.' . $field->id)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif($field->field_type === 'checkbox')
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input type="hidden" name="{{ $fieldName }}" value="0">
                            <input type="checkbox" class="form-check-input" id="{{ $fieldId }}" name="{{ $fieldName }}" value="1" {{ $currentValue ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $fieldId }}">
                                {{ $field->name }}
                                @if($field->is_required) <span class="text-danger">*</span> @endif
                            </label>
                        </div>
                        @error('custom_fields.' . $field->id)
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif($field->field_type === 'radio')
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            {{ $field->name }}
                            @if($field->is_required) <span class="text-danger">*</span> @endif
                        </label>
                        <div>
                            @foreach($field->options ?? [] as $option)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="{{ $fieldName }}" id="{{ $fieldId }}_{{ $loop->index }}" value="{{ $option }}" {{ $currentValue === $option ? 'checked' : '' }} {{ $field->is_required ? 'required' : '' }}>
                                    <label class="form-check-label" for="{{ $fieldId }}_{{ $loop->index }}">{{ $option }}</label>
                                </div>
                            @endforeach
                        </div>
                        @error('custom_fields.' . $field->id)
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif($field->field_type === 'select')
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="{{ $fieldId }}">
                            {{ $field->name }}
                            @if($field->is_required) <span class="text-danger">*</span> @endif
                        </label>
                        <select class="form-select @error('custom_fields.' . $field->id) is-invalid @enderror" id="{{ $fieldId }}" name="{{ $fieldName }}" {{ $field->is_required ? 'required' : '' }}>
                            <option value="">Select {{ $field->name }}</option>
                            @foreach($field->options ?? [] as $option)
                                <option value="{{ $option }}" {{ $currentValue === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('custom_fields.' . $field->id)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif($field->field_type === 'date')
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="{{ $fieldId }}">
                            {{ $field->name }}
                            @if($field->is_required) <span class="text-danger">*</span> @endif
                        </label>
                        <input type="date" class="form-control @error('custom_fields.' . $field->id) is-invalid @enderror" id="{{ $fieldId }}" name="{{ $fieldName }}" value="{{ $currentValue }}" {{ $field->is_required ? 'required' : '' }}>
                        @error('custom_fields.' . $field->id)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif($field->field_type === 'number')
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="{{ $fieldId }}">
                            {{ $field->name }}
                            @if($field->is_required) <span class="text-danger">*</span> @endif
                        </label>
                        <input type="number" class="form-control @error('custom_fields.' . $field->id) is-invalid @enderror" id="{{ $fieldId }}" name="{{ $fieldName }}" value="{{ $currentValue }}" {{ $field->is_required ? 'required' : '' }}>
                        @error('custom_fields.' . $field->id)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif($field->field_type === 'file')
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="{{ $fieldId }}">
                            {{ $field->name }}
                            @if($field->is_required && empty($currentValue)) <span class="text-danger">*</span> @endif
                        </label>
                        <input type="file" class="form-control @error('custom_fields.' . $field->id) is-invalid @enderror" id="{{ $fieldId }}" name="{{ $fieldName }}" {{ $field->is_required && empty($currentValue) ? 'required' : '' }}>
                        @if(!empty($currentValue))
                            <small class="text-muted">Current file: <a href="{{ asset('storage/' . $currentValue) }}" target="_blank">View</a></small>
                        @endif
                        @error('custom_fields.' . $field->id)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    {{-- Default: text --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="{{ $fieldId }}">
                            {{ $field->name }}
                            @if($field->is_required) <span class="text-danger">*</span> @endif
                        </label>
                        <input type="text" class="form-control @error('custom_fields.' . $field->id) is-invalid @enderror" id="{{ $fieldId }}" name="{{ $fieldName }}" value="{{ $currentValue }}" {{ $field->is_required ? 'required' : '' }}>
                        @error('custom_fields.' . $field->id)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif
