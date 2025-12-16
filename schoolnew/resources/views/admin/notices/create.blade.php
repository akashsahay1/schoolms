@extends('layouts.app')

@section('title', 'Create Notice')
@section('page-title', 'Create Notice')

@section('breadcrumb')
    <li class="breadcrumb-item">Communication</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.notices.index') }}">Notices</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Create New Notice</h5>
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

                    <form action="{{ route('admin.notices.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="6" required>{{ old('content') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Publish Date <span class="text-danger">*</span></label>
                                <input type="date" name="publish_date" class="form-control" value="{{ old('publish_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                                <small class="text-muted">Leave empty for no expiry</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Attachment</label>
                                <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <small class="text-muted">Max 5MB</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Audience <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($audiences as $key => $label)
                                        <div class="form-check">
                                            <input type="checkbox" name="target_audience[]" value="{{ $key }}" class="form-check-input" id="audience_{{ $key }}" {{ in_array($key, old('target_audience', ['all'])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="audience_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Classes (Optional)</label>
                                <select name="target_classes[]" class="form-select" multiple size="4">
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ in_array($class->id, old('target_classes', [])) ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Leave empty for all classes. Hold Ctrl to select multiple.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_published" value="1" class="form-check-input" id="is_published" {{ old('is_published', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">Publish Immediately</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="send_email" value="1" class="form-check-input" id="send_email" {{ old('send_email') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_email">Send Email Notification</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="send_sms" value="1" class="form-check-input" id="send_sms" {{ old('send_sms') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_sms">Send SMS Notification</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Notice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
