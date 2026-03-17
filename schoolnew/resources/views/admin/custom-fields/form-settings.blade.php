@extends('layouts.app')

@section('title', 'Form Field Settings')

@section('page-title', 'Form Field Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.custom-fields.index') }}">Custom Fields</a></li>
    <li class="breadcrumb-item active">Form Settings</li>
@endsection

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
                        <h5><i data-feather="users" class="me-2" style="width: 18px; height: 18px;"></i> Student Form Fields</h5>
                        <span class="badge badge-light-warning">{{ count($studentFields) }} fields</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th class="text-center" style="width: 100px;">Visible</th>
                                    <th class="text-center" style="width: 100px;">Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentSection = ''; @endphp
                                @foreach($studentFields as $fieldKey => $fieldInfo)
                                    @if($fieldInfo['section'] !== $currentSection)
                                        @php $currentSection = $fieldInfo['section']; @endphp
                                        <tr class="table-light">
                                            <td colspan="3"><strong>{{ $currentSection }}</strong></td>
                                        </tr>
                                    @endif
                                    @php
                                        $isVisible = $studentConfig[$fieldKey]['visible'] ?? true;
                                        $isRequired = $studentConfig[$fieldKey]['required'] ?? false;
                                    @endphp
                                    <tr>
                                        <td>{{ $fieldInfo['label'] }}</td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input type="checkbox" class="form-check-input toggle-visible" name="student[{{ $fieldKey }}][visible]" value="1" {{ $isVisible ? 'checked' : '' }} data-group="student" data-field="{{ $fieldKey }}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input type="checkbox" class="form-check-input toggle-required" name="student[{{ $fieldKey }}][required]" value="1" {{ $isRequired ? 'checked' : '' }} data-group="student" data-field="{{ $fieldKey }}" {{ !$isVisible ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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
                        <h5><i data-feather="book-open" class="me-2" style="width: 18px; height: 18px;"></i> Teacher Form Fields</h5>
                        <span class="badge badge-light-info">{{ count($teacherFields) }} fields</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th class="text-center" style="width: 100px;">Visible</th>
                                    <th class="text-center" style="width: 100px;">Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentSection = ''; @endphp
                                @foreach($teacherFields as $fieldKey => $fieldInfo)
                                    @if($fieldInfo['section'] !== $currentSection)
                                        @php $currentSection = $fieldInfo['section']; @endphp
                                        <tr class="table-light">
                                            <td colspan="3"><strong>{{ $currentSection }}</strong></td>
                                        </tr>
                                    @endif
                                    @php
                                        $isVisible = $teacherConfig[$fieldKey]['visible'] ?? true;
                                        $isRequired = $teacherConfig[$fieldKey]['required'] ?? false;
                                    @endphp
                                    <tr>
                                        <td>{{ $fieldInfo['label'] }}</td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input type="checkbox" class="form-check-input toggle-visible" name="teacher[{{ $fieldKey }}][visible]" value="1" {{ $isVisible ? 'checked' : '' }} data-group="teacher" data-field="{{ $fieldKey }}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input type="checkbox" class="form-check-input toggle-required" name="teacher[{{ $fieldKey }}][required]" value="1" {{ $isRequired ? 'checked' : '' }} data-group="teacher" data-field="{{ $fieldKey }}" {{ !$isVisible ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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
                            <i data-feather="save" class="me-1"></i> Save Settings
                        </button>
                        <a href="{{ route('admin.custom-fields.index') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Back to Custom Fields
                        </a>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                        Core fields (First Name, Gender, Date of Birth, etc.) are always visible and required. Only optional fields can be configured here.
                        Teacher settings apply to all staff (teachers and non-teaching staff).
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

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
