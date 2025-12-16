@extends('layouts.app')

@section('title', 'Create Event')
@section('page-title', 'Create Event')

@section('breadcrumb')
    <li class="breadcrumb-item">Communication</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Create New Event</h5>
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

                    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Event Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" id="event_type" required>
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" data-color="{{ $colors[$key] ?? '#3498db' }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">End Time</label>
                                <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Venue</label>
                                <input type="text" name="venue" class="form-control" value="{{ old('venue') }}" placeholder="Event location">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Calendar Color</label>
                                <input type="color" name="color" class="form-control form-control-color w-100" id="color_picker" value="{{ old('color', '#3498db') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Event Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_holiday" value="1" class="form-check-input" id="is_holiday" {{ old('is_holiday') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_holiday">Mark as Holiday</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_public" value="1" class="form-check-input" id="is_public" {{ old('is_public', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">Show on Public Website</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Audience</label>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="target_audience[]" value="all" class="form-check-input" id="audience_all" checked>
                                        <label class="form-check-label" for="audience_all">All</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="target_audience[]" value="students" class="form-check-input" id="audience_students">
                                        <label class="form-check-label" for="audience_students">Students</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="target_audience[]" value="parents" class="form-check-input" id="audience_parents">
                                        <label class="form-check-label" for="audience_parents">Parents</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="target_audience[]" value="teachers" class="form-check-input" id="audience_teachers">
                                        <label class="form-check-label" for="audience_teachers">Teachers</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gallery Photos</label>
                                <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                                <small class="text-muted">You can select multiple images</small>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
jQuery(document).ready(function() {
    jQuery('#event_type').on('change', function() {
        var color = jQuery(this).find(':selected').data('color');
        jQuery('#color_picker').val(color);
    });
});
</script>
@endpush
@endsection
