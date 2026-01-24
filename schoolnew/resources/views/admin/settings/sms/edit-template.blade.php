@extends('layouts.app')

@section('title', 'Edit SMS Template')

@section('page-title', 'Settings - Edit SMS Template')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.sms.templates') }}">SMS Templates</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Edit SMS Template</h5>
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

                <form action="{{ route('admin.settings.sms.templates.update', $template) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $template->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror">
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category', $template->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="4" required>{{ old('content', $template->content) }}</textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Max 500 characters. Use variables like {student_name}</small>
                            <small class="text-muted"><span id="charCount">0</span>/500</small>
                        </div>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Template
                        </button>
                        <a href="{{ route('admin.settings.sms.templates') }}" class="btn btn-secondary">
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
                <h5>Available Variables</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Click to insert into content:</p>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($variables as $key => $label)
                        <button type="button" class="btn btn-sm btn-outline-primary variable-btn" data-var="{{ $key }}">
                            {{ '{' . $key . '}' }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Character count
    var contentField = jQuery('textarea[name="content"]');
    var charCount = jQuery('#charCount');

    contentField.on('input', function() {
        charCount.text(jQuery(this).val().length);
    });
    charCount.text(contentField.val().length);

    // Insert variable
    jQuery('.variable-btn').on('click', function() {
        var varName = '{' + jQuery(this).data('var') + '}';
        var textarea = jQuery('textarea[name="content"]');
        var cursorPos = textarea[0].selectionStart;
        var textBefore = textarea.val().substring(0, cursorPos);
        var textAfter = textarea.val().substring(cursorPos);

        textarea.val(textBefore + varName + textAfter);
        textarea.focus();
        textarea[0].selectionStart = textarea[0].selectionEnd = cursorPos + varName.length;
        charCount.text(textarea.val().length);
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
