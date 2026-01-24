@extends('layouts.portal')

@section('title', 'Homework Details')
@section('page-title', 'Homework Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.homework.index') }}">Homework</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($homework->title, 20) }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Homework Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5>{{ $homework->title }}</h5>
                        @php
                            $isOverdue = $homework->submission_date < now() && (!$submission || !in_array($submission->status, ['submitted', 'evaluated']));
                        @endphp
                        @if($isOverdue)
                            <span class="badge bg-danger">Overdue</span>
                        @elseif($submission && in_array($submission->status, ['submitted', 'evaluated', 'late']))
                            <span class="badge bg-success">Submitted</span>
                        @elseif($homework->submission_date->isToday())
                            <span class="badge bg-warning">Due Today</span>
                        @elseif($homework->submission_date->isTomorrow())
                            <span class="badge bg-info">Due Tomorrow</span>
                        @else
                            <span class="badge bg-primary">{{ now()->diffInDays($homework->submission_date, false) }} days left</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Homework Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong><i class="fa fa-book me-2"></i>Subject:</strong> {{ $homework->subject->name ?? '-' }}</p>
                            <p><strong><i class="fa fa-user me-2"></i>Teacher:</strong> {{ $homework->teacher->first_name ?? '' }} {{ $homework->teacher->last_name ?? '' }}</p>
                            <p><strong><i class="fa fa-graduation-cap me-2"></i>Class:</strong> {{ $homework->schoolClass->name ?? '-' }} {{ $homework->section ? '- ' . $homework->section->name : '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fa fa-calendar-plus me-2"></i>Assigned:</strong> {{ $homework->homework_date->format('M d, Y') }}</p>
                            <p><strong><i class="fa fa-calendar-times me-2"></i>Due Date:</strong> {{ $homework->submission_date->format('M d, Y') }}</p>
                            @if($homework->max_marks)
                                <p><strong><i class="fa fa-star me-2"></i>Max Marks:</strong> {{ $homework->max_marks }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h6>Description</h6>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($homework->description)) !!}
                        </div>
                    </div>

                    <!-- Attachment -->
                    @if($homework->attachment)
                        <div class="mb-4">
                            <h6>Attached File</h6>
                            <a href="{{ Storage::url($homework->attachment) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fa fa-download me-2"></i> Download Attachment
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submission Form -->
            @if(!$submission || !in_array($submission->status, ['submitted', 'evaluated', 'late']))
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Submit Homework</h5>
                    </div>
                    <div class="card-body">
                        @if($isOverdue)
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                This homework is overdue. Your submission will be marked as late.
                            </div>
                        @endif

                        <form action="{{ route('portal.homework.submit', $homework) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Your Answer / Notes</label>
                                <textarea name="submission_text" class="form-control @error('submission_text') is-invalid @enderror" rows="6" placeholder="Write your answer or notes here...">{{ old('submission_text') }}</textarea>
                                @error('submission_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload File (Optional)</label>
                                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                                <small class="text-muted">Allowed: PDF, DOC, DOCX, JPG, PNG, ZIP (Max: 5MB)</small>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-paper-plane me-2"></i> Submit Homework
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Submission Status -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Submission Status</h5>
                </div>
                <div class="card-body">
                    @if($submission)
                        <div class="text-center mb-3">
                            @if($submission->status == 'evaluated')
                                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fa fa-check fa-2x"></i>
                                </div>
                                <h6 class="mt-3 text-success">Evaluated</h6>
                            @elseif($submission->status == 'submitted')
                                <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fa fa-paper-plane fa-2x"></i>
                                </div>
                                <h6 class="mt-3 text-info">Submitted</h6>
                            @elseif($submission->status == 'late')
                                <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fa fa-clock fa-2x"></i>
                                </div>
                                <h6 class="mt-3 text-warning">Late Submission</h6>
                            @else
                                <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fa fa-hourglass-half fa-2x"></i>
                                </div>
                                <h6 class="mt-3 text-secondary">{{ ucfirst($submission->status) }}</h6>
                            @endif
                        </div>

                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Submitted:</strong></td>
                                <td>{{ $submission->submitted_date ? $submission->submitted_date->format('M d, Y h:i A') : '-' }}</td>
                            </tr>
                            @if($submission->marks_obtained !== null)
                                <tr>
                                    <td><strong>Marks:</strong></td>
                                    <td>
                                        <strong class="text-primary">{{ $submission->marks_obtained }}</strong> / {{ $homework->max_marks }}
                                        @php
                                            $percentage = $homework->max_marks > 0
                                                ? ($submission->marks_obtained / $homework->max_marks) * 100
                                                : 0;
                                        @endphp
                                        <span class="badge bg-{{ $percentage >= 50 ? 'success' : 'danger' }}">
                                            {{ number_format($percentage, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endif
                            @if($submission->evaluated_at)
                                <tr>
                                    <td><strong>Evaluated:</strong></td>
                                    <td>{{ $submission->evaluated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            @endif
                        </table>

                        @if($submission->remarks)
                            <div class="mt-3">
                                <h6>Teacher's Remarks</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $submission->remarks }}
                                </div>
                            </div>
                        @endif

                        @if($submission->attachment)
                            <div class="mt-3">
                                <h6>Your Attachment</h6>
                                <a href="{{ Storage::url($submission->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fa fa-download me-2"></i> Download
                                </a>
                            </div>
                        @endif

                        @if($submission->submission_text)
                            <div class="mt-3">
                                <h6>Your Answer</h6>
                                <div class="p-3 bg-light rounded" style="max-height: 200px; overflow-y: auto;">
                                    {!! nl2br(e($submission->submission_text)) !!}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center">
                            <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fa fa-clock fa-2x"></i>
                            </div>
                            <h6 class="mt-3 text-warning">Not Submitted</h6>
                            <p class="text-muted">You haven't submitted this homework yet.</p>
                            @if($isOverdue)
                                <p class="text-danger small">This homework is overdue!</p>
                            @else
                                <p class="text-muted small">Due in {{ now()->diffForHumans($homework->submission_date, true) }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('portal.homework.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="fa fa-arrow-left me-2"></i> Back to Homework
                    </a>
                    <a href="{{ route('portal.homework.pending') }}" class="btn btn-outline-primary w-100">
                        <i class="fa fa-list me-2"></i> View Pending
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
