@extends('layouts.portal')

@section('title', 'Homework')
@section('page-title', 'My Homework')

@section('breadcrumb')
    <li class="breadcrumb-item active">Homework</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-md-3 col-sm-6">
            <div class="card small-widget">
                <div class="card-body primary">
                    <span class="f-light">Total Homework</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $stats['total'] }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#file-text') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card small-widget">
                <div class="card-body warning">
                    <span class="f-light">Pending</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $stats['pending'] }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#clock') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card small-widget">
                <div class="card-body success">
                    <span class="f-light">Submitted</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $stats['submitted'] }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#check-circle') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card small-widget">
                <div class="card-body danger">
                    <span class="f-light">Overdue</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $stats['overdue'] }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#alert-triangle') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Filter Homework</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('portal.homework') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Subject</label>
                            <select name="subject_id" class="form-select">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary me-2">Apply Filter</button>
                            <a href="{{ route('portal.homework') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Homework List -->
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Homework List</h5>
                </div>
                <div class="card-body">
                    @if($homeworks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Subject</th>
                                        <th>Assigned Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($homeworks as $index => $homework)
                                        @php
                                            $submission = $submissionsByHomework->get($homework->id);
                                            $isOverdue = $homework->submission_date < now() && (!$submission || !in_array($submission->status, ['submitted', 'evaluated']));
                                        @endphp
                                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                            <td>{{ $homeworks->firstItem() + $index }}</td>
                                            <td>
                                                <a href="{{ route('portal.homework.show', $homework) }}" class="text-primary fw-medium">
                                                    {{ $homework->title }}
                                                </a>
                                                @if($homework->attachment)
                                                    <i class="fa fa-paperclip ms-1 text-muted" title="Has attachment"></i>
                                                @endif
                                            </td>
                                            <td>{{ $homework->subject->name ?? '-' }}</td>
                                            <td>{{ $homework->homework_date->format('M d, Y') }}</td>
                                            <td>
                                                {{ $homework->submission_date->format('M d, Y') }}
                                                @if($isOverdue)
                                                    <br><small class="text-danger">Overdue!</small>
                                                @elseif($homework->submission_date->isToday())
                                                    <br><small class="text-warning">Due Today</small>
                                                @elseif($homework->submission_date->isTomorrow())
                                                    <br><small class="text-info">Due Tomorrow</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submission)
                                                    @if($submission->status == 'evaluated')
                                                        <span class="badge bg-success">Evaluated</span>
                                                        @if($submission->marks_obtained !== null)
                                                            <br><small>{{ $submission->marks_obtained }}/{{ $homework->max_marks }}</small>
                                                        @endif
                                                    @elseif($submission->status == 'submitted')
                                                        <span class="badge bg-info">Submitted</span>
                                                    @elseif($submission->status == 'late')
                                                        <span class="badge bg-warning">Late Submission</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($submission->status) }}</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('portal.homework.show', $homework) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-eye me-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $homeworks->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#file-text') }}"></use>
                            </svg>
                            <h6 class="mt-3 text-muted">No Homework Found</h6>
                            <p class="text-muted">No homework has been assigned to your class yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
