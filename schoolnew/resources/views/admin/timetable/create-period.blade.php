@extends('layouts.app')

@section('title', 'Add Period')

@section('page-title', 'Add Period')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.timetable.index') }}">Timetable</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.timetable.periods') }}">Periods</a></li>
    <li class="breadcrumb-item active">Add Period</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>Add New Period</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.timetable.periods.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Period Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Period 1, Break, Lunch" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="class" {{ old('type') == 'class' ? 'selected' : '' }}>Class</option>
                                <option value="break" {{ old('type') == 'break' ? 'selected' : '' }}>Break</option>
                                <option value="lunch" {{ old('type') == 'lunch' ? 'selected' : '' }}>Lunch</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Order <span class="text-danger">*</span></label>
                            <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 1) }}" min="1" required>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Display order in timetable</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.timetable.periods') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Save Period
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
