@extends('layouts.app')

@section('title', 'Create Bulk Message')

@section('page-title', 'Create Bulk Message')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.messaging.bulk.index') }}">Bulk Messages</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>New Bulk Message</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.messaging.bulk.store') }}" method="POST" id="bulkMessageForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Enter message title" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Message Type <span class="text-danger">*</span></label>
                            <select name="message_type" class="form-select" required>
                                @foreach($messageTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('message_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Recipient Type <span class="text-danger">*</span></label>
                            <select name="recipient_type" id="recipientType" class="form-select" required>
                                @foreach($recipientTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('recipient_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Class/Section Selection (shown only when recipient_type is class_wise) -->
                    <div id="classWiseSection" class="mb-3" style="display: none;">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="mb-3">Select Classes & Sections</h6>
                                <div class="row">
                                    @foreach($classes as $class)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input class-checkbox" type="checkbox" name="class_ids[]" value="{{ $class->id }}" id="class{{ $class->id }}" {{ in_array($class->id, old('class_ids', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="class{{ $class->id }}">
                                                    {{ $class->name }}
                                                </label>
                                            </div>
                                            @if($class->sections->count() > 0)
                                                <div class="ms-4 mt-2">
                                                    @foreach($class->sections as $section)
                                                        <div class="form-check">
                                                            <input class="form-check-input section-checkbox" type="checkbox" name="section_ids[]" value="{{ $section->id }}" id="section{{ $section->id }}" data-class="{{ $class->id }}" {{ in_array($section->id, old('section_ids', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="section{{ $section->id }}">
                                                                {{ $section->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="6" placeholder="Enter your message here..." required>{{ old('message') }}</textarea>
                        <small class="text-muted">Character count: <span id="charCount">0</span> | SMS segments: <span id="smsSegments">0</span></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Schedule Send (Optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
                        <small class="text-muted">Leave empty to save as draft or send immediately</small>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-secondary" name="action" value="draft">
                            <i data-feather="save" class="me-1"></i> Save as Draft
                        </button>
                        <button type="submit" class="btn btn-primary" name="send_now" value="1">
                            <i data-feather="send" class="me-1"></i> Send Now
                        </button>
                        <a href="{{ route('admin.messaging.bulk.index') }}" class="btn btn-light">
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
                <h5>Tips</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i>
                        <strong>SMS:</strong> Each SMS segment is 160 characters (70 for Unicode)
                    </li>
                    <li class="mb-2">
                        <i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i>
                        <strong>Email:</strong> Subject line is taken from the title
                    </li>
                    <li class="mb-2">
                        <i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i>
                        <strong>All Channels:</strong> Message is sent via SMS, Email, and In-App Notification
                    </li>
                    <li class="mb-2">
                        <i data-feather="alert-circle" class="text-warning me-2" style="width: 16px; height: 16px;"></i>
                        Scheduled messages can be edited or cancelled before the scheduled time
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Message Preview</h5>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded" id="messagePreview">
                    <p class="text-muted mb-0">Your message preview will appear here...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') feather.replace();

    // Toggle class/section selection based on recipient type
    function toggleClassSection() {
        var recipientType = jQuery('#recipientType').val();
        if (recipientType === 'class_wise') {
            jQuery('#classWiseSection').slideDown();
        } else {
            jQuery('#classWiseSection').slideUp();
        }
    }

    jQuery('#recipientType').on('change', toggleClassSection);
    toggleClassSection();

    // Character count and SMS segments
    function updateCharCount() {
        var text = jQuery('textarea[name="message"]').val();
        var length = text.length;
        var hasUnicode = /[^\x00-\x7F]/.test(text);
        var segmentSize = hasUnicode ? 70 : 160;
        var segments = Math.ceil(length / segmentSize) || 0;

        jQuery('#charCount').text(length);
        jQuery('#smsSegments').text(segments);
    }

    jQuery('textarea[name="message"]').on('input', function() {
        updateCharCount();
        jQuery('#messagePreview').html('<p class="mb-0">' + (jQuery(this).val() || '<span class="text-muted">Your message preview will appear here...</span>') + '</p>');
    });

    updateCharCount();

    // Auto-select sections when class is checked
    jQuery('.class-checkbox').on('change', function() {
        var classId = jQuery(this).val();
        var isChecked = jQuery(this).is(':checked');
        jQuery('.section-checkbox[data-class="' + classId + '"]').prop('checked', isChecked);
    });
});
</script>
@endpush
