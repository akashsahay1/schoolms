@extends('layouts.app')

@section('title', 'Form Field Settings')

@section('page-title', 'Form Field Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.custom-fields.index') }}">Custom Fields</a></li>
    <li class="breadcrumb-item active">Form Settings</li>
@endsection

@push('styles')
<style>
    .form-settings-table {
        border-collapse: collapse;
    }
    .form-settings-table thead th {
        background: #f4f5f7;
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
    }
    .form-settings-table .section-header td {
        background: #7366ff;
        color: #fff;
        padding: 9px 16px;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.3px;
        border: none;
    }
    .form-settings-table .section-spacer td {
        background: #fff;
        padding: 0;
        height: 12px;
        border: none;
    }
    .form-settings-table .field-row td {
        padding: 11px 16px;
        vertical-align: middle;
        background: #fafbfc;
        border-bottom: 1px solid #eee;
    }
    .form-settings-table .field-row:hover td {
        background: #eef0ff;
    }
    .form-settings-table .form-check-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<form action="{{ route('admin.custom-fields.update-form-settings') }}" method="POST">
    @csrf
    @method('PUT')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Student Form Fields -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="icon-user me-2"></i> Student Form Fields</h5>
                        <span class="badge badge-light-warning">{{ count($studentFields) }} fields</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table form-settings-table mb-0">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th class="text-center" style="width: 100px;">Visible</th>
                                    <th class="text-center" style="width: 100px;">Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentSection = ''; $isFirst = true; @endphp
                                @foreach($studentFields as $fieldKey => $fieldInfo)
                                    @if($fieldInfo['section'] !== $currentSection)
                                        @if(!$isFirst)
                                            <tr class="section-spacer"><td colspan="3"></td></tr>
                                        @endif
                                        @php $currentSection = $fieldInfo['section']; $isFirst = false; @endphp
                                        <tr class="section-header">
                                            <td colspan="3">{{ $currentSection }}</td>
                                        </tr>
                                    @endif
                                    @php
                                        $isVisible = $studentConfig[$fieldKey]['visible'] ?? true;
                                        $isRequired = $studentConfig[$fieldKey]['required'] ?? false;
                                    @endphp
                                    <tr class="field-row">
                                        <td>{{ $fieldInfo['label'] }}</td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center mb-0">
                                                <input type="checkbox" class="form-check-input toggle-visible" name="student[{{ $fieldKey }}][visible]" value="1" {{ $isVisible ? 'checked' : '' }} data-group="student" data-field="{{ $fieldKey }}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center mb-0">
                                                <input type="checkbox" class="form-check-input toggle-required" name="student[{{ $fieldKey }}][required]" value="1" {{ $isRequired ? 'checked' : '' }} data-group="student" data-field="{{ $fieldKey }}" {{ !$isVisible ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($studentCustomFields->count() > 0)
                                    <tr class="section-spacer"><td colspan="3"></td></tr>
                                    <tr class="section-header">
                                        <td colspan="3">Additional Information (Custom Fields)</td>
                                    </tr>
                                    @foreach($studentCustomFields as $cf)
                                        <tr class="field-row">
                                            <td>
                                                {{ $cf->name }}
                                                <small class="text-muted ms-1">({{ ucfirst($cf->field_type) }})</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center mb-0">
                                                    <input type="checkbox" class="form-check-input toggle-visible" name="custom_field[{{ $cf->id }}][visible]" value="1" {{ $cf->is_active && !$cf->trashed() ? 'checked' : '' }} data-group="custom_student" data-field="{{ $cf->id }}">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center mb-0">
                                                    <input type="checkbox" class="form-check-input toggle-required" name="custom_field[{{ $cf->id }}][required]" value="1" {{ $cf->is_required ? 'checked' : '' }} data-group="custom_student" data-field="{{ $cf->id }}" {{ !$cf->is_active || $cf->trashed() ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Form Fields -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="icon-book-open me-2"></i> Teacher Form Fields</h5>
                        <span class="badge badge-light-info">{{ count($teacherFields) }} fields</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table form-settings-table mb-0">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th class="text-center" style="width: 100px;">Visible</th>
                                    <th class="text-center" style="width: 100px;">Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentSection = ''; $isFirst = true; @endphp
                                @foreach($teacherFields as $fieldKey => $fieldInfo)
                                    @if($fieldInfo['section'] !== $currentSection)
                                        @if(!$isFirst)
                                            <tr class="section-spacer"><td colspan="3"></td></tr>
                                        @endif
                                        @php $currentSection = $fieldInfo['section']; $isFirst = false; @endphp
                                        <tr class="section-header">
                                            <td colspan="3">{{ $currentSection }}</td>
                                        </tr>
                                    @endif
                                    @php
                                        $isVisible = $teacherConfig[$fieldKey]['visible'] ?? true;
                                        $isRequired = $teacherConfig[$fieldKey]['required'] ?? false;
                                    @endphp
                                    <tr class="field-row">
                                        <td>{{ $fieldInfo['label'] }}</td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center mb-0">
                                                <input type="checkbox" class="form-check-input toggle-visible" name="teacher[{{ $fieldKey }}][visible]" value="1" {{ $isVisible ? 'checked' : '' }} data-group="teacher" data-field="{{ $fieldKey }}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center mb-0">
                                                <input type="checkbox" class="form-check-input toggle-required" name="teacher[{{ $fieldKey }}][required]" value="1" {{ $isRequired ? 'checked' : '' }} data-group="teacher" data-field="{{ $fieldKey }}" {{ !$isVisible ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($teacherCustomFields->count() > 0)
                                    <tr class="section-spacer"><td colspan="3"></td></tr>
                                    <tr class="section-header">
                                        <td colspan="3">Additional Information (Custom Fields)</td>
                                    </tr>
                                    @foreach($teacherCustomFields as $cf)
                                        <tr class="field-row">
                                            <td>
                                                {{ $cf->name }}
                                                <small class="text-muted ms-1">({{ ucfirst($cf->field_type) }})</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center mb-0">
                                                    <input type="checkbox" class="form-check-input toggle-visible" name="custom_field[{{ $cf->id }}][visible]" value="1" {{ $cf->is_active && !$cf->trashed() ? 'checked' : '' }} data-group="custom_teacher" data-field="{{ $cf->id }}">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center mb-0">
                                                    <input type="checkbox" class="form-check-input toggle-required" name="custom_field[{{ $cf->id }}][required]" value="1" {{ $cf->is_required ? 'checked' : '' }} data-group="custom_teacher" data-field="{{ $cf->id }}" {{ !$cf->is_active || $cf->trashed() ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Form Fields -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="icon-briefcase me-2"></i> Staff Form Fields</h5>
                        <span class="badge badge-light-secondary">{{ count($staffFields) }} fields</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table form-settings-table mb-0">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th class="text-center" style="width: 100px;">Visible</th>
                                    <th class="text-center" style="width: 100px;">Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentSection = ''; $isFirst = true; @endphp
                                @foreach($staffFields as $fieldKey => $fieldInfo)
                                    @if($fieldInfo['section'] !== $currentSection)
                                        @if(!$isFirst)
                                            <tr class="section-spacer"><td colspan="3"></td></tr>
                                        @endif
                                        @php $currentSection = $fieldInfo['section']; $isFirst = false; @endphp
                                        <tr class="section-header">
                                            <td colspan="3">{{ $currentSection }}</td>
                                        </tr>
                                    @endif
                                    @php
                                        $isVisible = $staffConfig[$fieldKey]['visible'] ?? true;
                                        $isRequired = $staffConfig[$fieldKey]['required'] ?? false;
                                    @endphp
                                    <tr class="field-row">
                                        <td>{{ $fieldInfo['label'] }}</td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center mb-0">
                                                <input type="checkbox" class="form-check-input toggle-visible" name="staff[{{ $fieldKey }}][visible]" value="1" {{ $isVisible ? 'checked' : '' }} data-group="staff" data-field="{{ $fieldKey }}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center mb-0">
                                                <input type="checkbox" class="form-check-input toggle-required" name="staff[{{ $fieldKey }}][required]" value="1" {{ $isRequired ? 'checked' : '' }} data-group="staff" data-field="{{ $fieldKey }}" {{ !$isVisible ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($staffCustomFields->count() > 0)
                                    <tr class="section-spacer"><td colspan="3"></td></tr>
                                    <tr class="section-header">
                                        <td colspan="3">Additional Information (Custom Fields)</td>
                                    </tr>
                                    @foreach($staffCustomFields as $cf)
                                        <tr class="field-row">
                                            <td>
                                                {{ $cf->name }}
                                                <small class="text-muted ms-1">({{ ucfirst($cf->field_type) }})</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center mb-0">
                                                    <input type="checkbox" class="form-check-input toggle-visible" name="custom_field[{{ $cf->id }}][visible]" value="1" {{ $cf->is_active && !$cf->trashed() ? 'checked' : '' }} data-group="custom_staff" data-field="{{ $cf->id }}">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center mb-0">
                                                    <input type="checkbox" class="form-check-input toggle-required" name="custom_field[{{ $cf->id }}][required]" value="1" {{ $cf->is_required ? 'checked' : '' }} data-group="custom_staff" data-field="{{ $cf->id }}" {{ !$cf->is_active || $cf->trashed() ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-save me-1"></i> Save Settings
                        </button>
                        <a href="{{ route('admin.custom-fields.index') }}" class="btn btn-secondary">
                            <i class="icon-arrow-left me-1"></i> Back to Custom Fields
                        </a>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="icon-info-alt me-1"></i>
                        All fields can be toggled. Uncheck "Visible" to hide a field. Check "Required" to make it mandatory.
                        Teacher and Staff have separate settings.
                    </small>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    jQuery('.toggle-visible').on('change', function() {
        var group = jQuery(this).data('group');
        var field = jQuery(this).data('field');
        var reqCheckbox = jQuery('.toggle-required[data-group="' + group + '"][data-field="' + field + '"]');
        if (!jQuery(this).is(':checked')) {
            reqCheckbox.prop('checked', false).prop('disabled', true);
        } else {
            reqCheckbox.prop('disabled', false);
        }
    });
});
</script>
@endpush
