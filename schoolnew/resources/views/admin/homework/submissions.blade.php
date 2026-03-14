@extends('layouts.app')

@section('title', 'Homework Submissions')

@section('page-title', 'Homework Submissions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.homework.index') }}">Homework</a></li>
    <li class="breadcrumb-item active">Submissions</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Homework Info Card -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-2">{{ $homework->title }}</h5>
                        <div class="d-flex flex-wrap gap-3">
                            <span><strong>Class:</strong> {{ $homework->schoolClass->name }}{{ $homework->section ? ' (' . $homework->section->name . ')' : '' }}</span>
                            <span><strong>Subject:</strong> {{ $homework->subject->name }}</span>
                            <span><strong>Due:</strong> {{ $homework->submission_date->format('d M Y') }}</span>
                            <span><strong>Max Marks:</strong> {{ $homework->max_marks }}</span>
                        </div>
                        @if($homework->description)
                            <p class="text-muted mt-2 mb-0">{{ Str::limit($homework->description, 150) }}</p>
                        @endif
                    </div>
                    <div class="col-md-4 text-end">
                        @if($homework->is_overdue)
                            <span class="badge badge-light-danger fs-6 px-3 py-2">Overdue</span>
                        @else
                            <span class="badge badge-light-success fs-6 px-3 py-2">Active</span>
                        @endif
                        @if($homework->attachment)
                            <a href="{{ asset('storage/' . $homework->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                                <i data-feather="paperclip" class="me-1"></i> Attachment
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row mb-3">
            <div class="col-md-3 col-6">
                <div class="card">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted mb-1">Total Students</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card">
                    <div class="card-body text-center py-3">
                        <h6 class="text-success mb-1">Submitted</h6>
                        <h3 class="mb-0 text-success">{{ $stats['submitted'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card">
                    <div class="card-body text-center py-3">
                        <h6 class="text-warning mb-1">Pending</h6>
                        <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card">
                    <div class="card-body text-center py-3">
                        <h6 class="text-info mb-1">Evaluated</h6>
                        <h3 class="mb-0 text-info">{{ $stats['evaluated'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Submissions List</h5>
                    <a href="{{ route('admin.homework.index') }}" class="btn btn-light btn-sm">
                        <i data-feather="arrow-left" class="me-1"></i> Back to Homework
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Admission No</th>
                                <th>Status</th>
                                <th>Submitted Date</th>
                                <th>Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $submission->student->full_name }}</strong></td>
                                    <td>{{ $submission->student->admission_no }}</td>
                                    <td>
                                        @if($submission->status === 'pending')
                                            <span class="badge badge-light-warning">Pending</span>
                                        @elseif($submission->status === 'submitted')
                                            <span class="badge badge-light-success">Submitted</span>
                                        @elseif($submission->status === 'late')
                                            <span class="badge badge-light-danger">Late</span>
                                        @elseif($submission->status === 'evaluated')
                                            <span class="badge badge-light-info">Evaluated</span>
                                        @endif
                                    </td>
                                    <td>{{ $submission->submitted_date ? $submission->submitted_date->format('d M Y h:i A') : '-' }}</td>
                                    <td>
                                        @if($submission->marks_obtained !== null)
                                            <strong>{{ $submission->marks_obtained }}/{{ $homework->max_marks }}</strong>
                                            <small class="text-muted">({{ $homework->max_marks > 0 ? round(($submission->marks_obtained / $homework->max_marks) * 100) : 0 }}%)</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($submission->status === 'submitted' || $submission->status === 'late')
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#evaluateModal{{ $submission->id }}">
                                                <i data-feather="check-circle" style="width: 14px; height: 14px;" class="me-1"></i> Evaluate
                                            </button>
                                        @elseif($submission->status === 'evaluated')
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#evaluateModal{{ $submission->id }}">
                                                <i data-feather="eye" style="width: 14px; height: 14px;" class="me-1"></i> View
                                            </button>
                                        @else
                                            <span class="text-muted small">Not submitted</span>
                                        @endif
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

<!-- Evaluate Modals -->
@foreach($submissions as $submission)
    @if($submission->status !== 'pending')
        <div class="modal fade" id="evaluateModal{{ $submission->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $submission->status === 'evaluated' ? 'Submission Details' : 'Evaluate Submission' }} - {{ $submission->student->full_name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.homework.evaluate-submission', $submission) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <!-- Student Info -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted">Student</small>
                                    <p class="mb-0 fw-bold">{{ $submission->student->full_name }} ({{ $submission->student->admission_no }})</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Submitted</small>
                                    <p class="mb-0">{{ $submission->submitted_date ? $submission->submitted_date->format('d M Y h:i A') : '-' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Status</small>
                                    <p class="mb-0">
                                        @if($submission->status === 'submitted')
                                            <span class="badge badge-light-success">Submitted</span>
                                        @elseif($submission->status === 'late')
                                            <span class="badge badge-light-danger">Late</span>
                                        @elseif($submission->status === 'evaluated')
                                            <span class="badge badge-light-info">Evaluated</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <!-- Student's Submission -->
                            @if($submission->submission_text)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Student's Answer</label>
                                    <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                        {!! nl2br(e($submission->submission_text)) !!}
                                    </div>
                                </div>
                            @endif

                            @if($submission->attachment)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Submitted Attachment(s)</label>
                                    @php
                                        $subAttachments = json_decode($submission->attachment, true);
                                        $allFiles = is_array($subAttachments) ? $subAttachments : [$submission->attachment];
                                        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                        $imageFiles = [];
                                        $otherFiles = [];
                                        foreach ($allFiles as $filePath) {
                                            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                            if (in_array($ext, $imageExts)) {
                                                $imageFiles[] = $filePath;
                                            } else {
                                                $otherFiles[] = $filePath;
                                            }
                                        }
                                    @endphp

                                    {{-- Image Gallery --}}
                                    @if(count($imageFiles) > 0)
                                        <div class="submission-gallery position-relative border rounded bg-dark text-center mb-2" style="min-height: 300px; max-height: 450px; overflow: hidden;" data-gallery-id="{{ $submission->id }}">
                                            @foreach($imageFiles as $imgIdx => $imgPath)
                                                <img src="{{ asset('storage/' . $imgPath) }}" class="gallery-image" data-index="{{ $imgIdx }}" style="max-width: 100%; max-height: 440px; object-fit: contain; display: {{ $imgIdx === 0 ? 'inline-block' : 'none' }}; margin: 5px auto;" alt="Submission Image {{ $imgIdx + 1 }}">
                                            @endforeach

                                            @if(count($imageFiles) > 1)
                                                <button type="button" class="btn btn-light btn-sm position-absolute gallery-prev" style="top: 50%; left: 10px; transform: translateY(-50%); opacity: 0.85; z-index: 2; border-radius: 50%; width: 36px; height: 36px; padding: 0;" data-gallery="{{ $submission->id }}">
                                                    <i data-feather="chevron-left" style="width: 20px; height: 20px;"></i>
                                                </button>
                                                <button type="button" class="btn btn-light btn-sm position-absolute gallery-next" style="top: 50%; right: 10px; transform: translateY(-50%); opacity: 0.85; z-index: 2; border-radius: 50%; width: 36px; height: 36px; padding: 0;" data-gallery="{{ $submission->id }}">
                                                    <i data-feather="chevron-right" style="width: 20px; height: 20px;"></i>
                                                </button>
                                            @endif

                                            <div class="position-absolute bg-dark bg-opacity-50 text-white px-2 py-1 rounded" style="bottom: 10px; left: 50%; transform: translateX(-50%); font-size: 13px; z-index: 2;">
                                                <span class="gallery-counter" data-gallery="{{ $submission->id }}">1</span> / {{ count($imageFiles) }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            @foreach($imageFiles as $imgIdx => $imgPath)
                                                <a href="{{ asset('storage/' . $imgPath) }}" target="_blank" class="btn btn-outline-primary btn-sm" download>
                                                    <i data-feather="download" style="width: 14px; height: 14px;" class="me-1"></i> Image {{ $imgIdx + 1 }} ({{ strtoupper(pathinfo($imgPath, PATHINFO_EXTENSION)) }})
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Non-image files --}}
                                    @if(count($otherFiles) > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($otherFiles as $fileIdx => $filePath)
                                                <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                    <i data-feather="download" style="width: 14px; height: 14px;" class="me-1"></i> File ({{ strtoupper(pathinfo($filePath, PATHINFO_EXTENSION)) }})
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if(!$submission->submission_text && !$submission->attachment)
                                <div class="alert alert-light text-center mb-3">
                                    <i data-feather="info" style="width: 16px; height: 16px;" class="me-1"></i>
                                    No text or file was submitted by the student.
                                </div>
                            @endif

                            <hr>

                            <!-- Evaluation Fields -->
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Marks <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="marks_obtained" class="form-control" min="0" max="{{ $homework->max_marks }}" value="{{ $submission->marks_obtained }}" required {{ $submission->status === 'evaluated' ? '' : '' }}>
                                        <span class="input-group-text">/ {{ $homework->max_marks }}</span>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-semibold">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="2" placeholder="Optional feedback for the student">{{ $submission->remarks }}</textarea>
                                </div>
                            </div>

                            @if($submission->status === 'evaluated' && $submission->evaluatedBy)
                                <div class="small text-muted">
                                    Evaluated by {{ $submission->evaluatedBy->name ?? 'Unknown' }} on {{ $submission->evaluated_at ? $submission->evaluated_at->format('d M Y h:i A') : '-' }}
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="check" style="width: 14px; height: 14px;" class="me-1"></i>
                                {{ $submission->status === 'evaluated' ? 'Update Evaluation' : 'Save Evaluation' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Gallery navigation
    jQuery('.gallery-prev, .gallery-next').on('click', function(e) {
        e.preventDefault();
        var galleryId = jQuery(this).data('gallery');
        var gallery = jQuery('[data-gallery-id="' + galleryId + '"]');
        var images = gallery.find('.gallery-image');
        var currentIndex = gallery.find('.gallery-image:visible').data('index');
        var totalImages = images.length;
        var direction = jQuery(this).hasClass('gallery-next') ? 1 : -1;
        var newIndex = (currentIndex + direction + totalImages) % totalImages;

        images.hide();
        images.filter('[data-index="' + newIndex + '"]').show();
        jQuery('.gallery-counter[data-gallery="' + galleryId + '"]').text(newIndex + 1);
    });
});
</script>
@endpush
