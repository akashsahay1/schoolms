@extends('layouts.app')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')

@section('breadcrumb')
    <li class="breadcrumb-item">Communication</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Edit Event</h5>
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

                    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Event Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ old('type', $event->type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $event->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $event->end_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">End Time</label>
                                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Venue</label>
                                <input type="text" name="venue" class="form-control" value="{{ old('venue', $event->venue) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Calendar Color</label>
                                <input type="color" name="color" class="form-control form-control-color w-100" value="{{ old('color', $event->color) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Event Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                @if($event->image)
                                    <small class="text-muted">Current: <a href="{{ asset('storage/' . $event->image) }}" target="_blank">View</a></small>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_holiday" value="1" class="form-check-input" id="is_holiday" {{ old('is_holiday', $event->is_holiday) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_holiday">Mark as Holiday</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_public" value="1" class="form-check-input" id="is_public" {{ old('is_public', $event->is_public) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">Show on Public Website</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Add More Photos</label>
                                <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                            </div>
                        </div>

                        @if($event->photos->count() > 0)
                            <div class="mb-3">
                                <label class="form-label">Existing Gallery Photos</label>
                                <div class="row g-3">
                                    @foreach($event->photos as $photo)
                                        <div class="col-md-2">
                                            <div class="position-relative">
                                                <img src="{{ $photo->image_url }}" alt="" class="img-fluid rounded" style="height: 100px; width: 100%; object-fit: cover;">
                                                <form action="{{ route('admin.events.photos.destroy', $photo) }}" method="POST" class="position-absolute top-0 end-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this photo?')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
