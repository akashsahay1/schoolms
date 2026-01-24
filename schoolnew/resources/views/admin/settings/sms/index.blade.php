@extends('layouts.app')

@section('title', 'SMS Settings')

@section('page-title', 'Settings - SMS Integration')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
    <li class="breadcrumb-item active">SMS</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-light-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Sent</h6>
                        <h3 class="mb-0 text-primary">{{ number_format($stats['total_sent']) }}</h3>
                    </div>
                    <div class="bg-primary rounded-circle p-3">
                        <i data-feather="send" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Today</h6>
                        <h3 class="mb-0 text-success">{{ number_format($stats['today_sent']) }}</h3>
                    </div>
                    <div class="bg-success rounded-circle p-3">
                        <i data-feather="calendar" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Pending</h6>
                        <h3 class="mb-0 text-warning">{{ number_format($stats['total_pending']) }}</h3>
                    </div>
                    <div class="bg-warning rounded-circle p-3">
                        <i data-feather="clock" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Failed</h6>
                        <h3 class="mb-0 text-danger">{{ number_format($stats['total_failed']) }}</h3>
                    </div>
                    <div class="bg-danger rounded-circle p-3">
                        <i data-feather="x-circle" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <form action="{{ route('admin.settings.sms.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>SMS Gateway Configuration</h5>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_enabled" class="form-check-input" id="is_enabled" {{ $settings->is_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_enabled">Enable SMS</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SMS Provider <span class="text-danger">*</span></label>
                            <select name="provider" class="form-select" id="smsProvider">
                                @foreach($providers as $key => $label)
                                    <option value="{{ $key }}" {{ $settings->provider === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sender ID</label>
                            <input type="text" name="sender_id" class="form-control" value="{{ old('sender_id', $settings->sender_id) }}" placeholder="SCHOOL">
                        </div>
                    </div>

                    <div class="provider-fields" id="twilioFields" style="{{ $settings->provider !== 'twilio' ? 'display: none;' : '' }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account SID</label>
                                <input type="text" name="account_sid" class="form-control" value="{{ old('account_sid', $settings->account_sid) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Auth Token</label>
                                <input type="password" name="auth_token" class="form-control" placeholder="{{ $settings->auth_token ? '********' : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">From Number</label>
                                <input type="text" name="from_number" class="form-control" value="{{ old('from_number', $settings->from_number) }}" placeholder="+1234567890">
                            </div>
                        </div>
                    </div>

                    <div class="provider-fields" id="apiKeyFields" style="{{ $settings->provider === 'twilio' ? 'display: none;' : '' }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">API Key</label>
                                <input type="password" name="api_key" class="form-control" placeholder="{{ $settings->api_key ? '********' : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">API Secret</label>
                                <input type="password" name="api_secret" class="form-control" placeholder="{{ $settings->api_secret ? '********' : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Auto-Send Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="send_on_admission" class="form-check-input" id="send_on_admission" {{ $settings->send_on_admission ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_on_admission">Send SMS on New Admission</label>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="send_on_fee_collection" class="form-check-input" id="send_on_fee_collection" {{ $settings->send_on_fee_collection ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_on_fee_collection">Send SMS on Fee Collection</label>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="send_on_attendance" class="form-check-input" id="send_on_attendance" {{ $settings->send_on_attendance ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_on_attendance">Send SMS on Absent Attendance</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="send_on_exam_result" class="form-check-input" id="send_on_exam_result" {{ $settings->send_on_exam_result ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_on_exam_result">Send SMS on Exam Result</label>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="send_on_leave_approval" class="form-check-input" id="send_on_leave_approval" {{ $settings->send_on_leave_approval ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_on_leave_approval">Send SMS on Leave Approval</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Message Templates</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Use variables like: {student_name}, {class}, {amount}, {date}</p>

                    <div class="mb-3">
                        <label class="form-label">Admission SMS Template</label>
                        <textarea name="admission_template" class="form-control" rows="2">{{ old('admission_template', $settings->admission_template) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fee Collection SMS Template</label>
                        <textarea name="fee_template" class="form-control" rows="2">{{ old('fee_template', $settings->fee_template) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attendance SMS Template</label>
                        <textarea name="attendance_template" class="form-control" rows="2">{{ old('attendance_template', $settings->attendance_template) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Exam Result SMS Template</label>
                        <textarea name="result_template" class="form-control" rows="2">{{ old('result_template', $settings->result_template) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Leave Approval SMS Template</label>
                        <textarea name="leave_template" class="form-control" rows="2">{{ old('leave_template', $settings->leave_template) }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="me-1"></i> Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Test SMS</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.sms.test') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="test_phone" class="form-control" placeholder="+91XXXXXXXXXX" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" {{ !$settings->isConfigured() ? 'disabled' : '' }}>
                        <i data-feather="send" class="me-1"></i> Send Test SMS
                    </button>
                    @if(!$settings->isConfigured())
                        <small class="text-warning d-block mt-2">Configure and enable SMS settings first.</small>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Quick Links</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.settings.sms.templates') }}" class="btn btn-outline-primary w-100 mb-2">
                    <i data-feather="file-text" class="me-1"></i> Manage Templates
                </a>
                <a href="{{ route('admin.settings.sms.logs') }}" class="btn btn-outline-secondary w-100">
                    <i data-feather="list" class="me-1"></i> View SMS Logs
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Available Variables</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li><code>{student_name}</code> - Student full name</li>
                    <li><code>{parent_name}</code> - Parent name</li>
                    <li><code>{class}</code> - Class name</li>
                    <li><code>{section}</code> - Section name</li>
                    <li><code>{amount}</code> - Fee amount</li>
                    <li><code>{due_date}</code> - Due date</li>
                    <li><code>{date}</code> - Current date</li>
                    <li><code>{school_name}</code> - School name</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Toggle provider fields
    jQuery('#smsProvider').on('change', function() {
        var provider = jQuery(this).val();

        if (provider === 'twilio') {
            jQuery('#twilioFields').show();
            jQuery('#apiKeyFields').hide();
        } else {
            jQuery('#twilioFields').hide();
            jQuery('#apiKeyFields').show();
        }
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
