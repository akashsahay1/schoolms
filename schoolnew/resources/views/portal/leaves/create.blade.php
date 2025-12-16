@extends('layouts.portal')

@section('title', 'Apply for Leave')
@section('page-title', 'Apply for Leave')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.leaves.index') }}">Leave Applications</a></li>
    <li class="breadcrumb-item active">Apply</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Leave Application Form</h5>
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

                    <form action="{{ route('portal.leaves.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Student Name</label>
                                <input type="text" class="form-control" value="{{ $student->full_name }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Class / Section</label>
                                <input type="text" class="form-control" value="{{ $student->schoolClass->name ?? '' }} - {{ $student->section->name ?? '' }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                                <select name="leave_type" class="form-select" required>
                                    <option value="">Select Leave Type</option>
                                    @foreach($leaveTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('leave_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">From Date <span class="text-danger">*</span></label>
                                <input type="date" name="from_date" class="form-control" value="{{ old('from_date') }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">To Date <span class="text-danger">*</span></label>
                                <input type="date" name="to_date" class="form-control" value="{{ old('to_date') }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="4" required placeholder="Please provide a detailed reason for your leave request...">{{ old('reason') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Attachment (Optional)</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Accepted formats: PDF, JPG, PNG. Max size: 2MB</small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('portal.leaves.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-paper-plane me-1"></i> Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
