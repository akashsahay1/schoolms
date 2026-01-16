@extends('layouts.portal')

@section('title', 'Exam Results')
@section('page-title', 'My Exam Results')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.exams') }}">Exams</a></li>
    <li class="breadcrumb-item active">Results</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Exam Selection -->
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Select Exam</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('portal.exams.results') }}" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Select Exam</label>
                            <select name="exam_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Select Exam --</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->name }} ({{ $exam->examType->name ?? '' }}) - {{ $exam->start_date->format('M Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">View Results</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($selectedExam && $result)
            <!-- Performance Summary -->
            <div class="col-md-3 col-sm-6">
                <div class="card small-widget">
                    <div class="card-body {{ $result['result'] == 'Pass' ? 'success' : 'danger' }}">
                        <span class="f-light">Result</span>
                        <div class="d-flex align-items-end gap-1">
                            <h4>{{ $result['result'] }}</h4>
                        </div>
                        <div class="bg-gradient">
                            <svg class="stroke-icon svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#' . ($result['result'] == 'Pass' ? 'check-circle' : 'x-circle')) }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card small-widget">
                    <div class="card-body primary">
                        <span class="f-light">Percentage</span>
                        <div class="d-flex align-items-end gap-1">
                            <h4>{{ $result['percentage'] }}%</h4>
                        </div>
                        <div class="bg-gradient">
                            <svg class="stroke-icon svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#trending-up') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card small-widget">
                    <div class="card-body warning">
                        <span class="f-light">Grade</span>
                        <div class="d-flex align-items-end gap-1">
                            <h4>{{ $result['grade'] }}</h4>
                        </div>
                        <div class="bg-gradient">
                            <svg class="stroke-icon svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#award') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card small-widget">
                    <div class="card-body info">
                        <span class="f-light">Class Rank</span>
                        <div class="d-flex align-items-end gap-1">
                            <h4>{{ $result['rank'] }}<small class="f-light"> / {{ $result['total_students'] }}</small></h4>
                        </div>
                        <div class="bg-gradient">
                            <svg class="stroke-icon svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#trophy') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ $selectedExam->name }} - Subject-wise Results</h5>
                            <a href="{{ route('portal.exams.report-card', ['exam_id' => $selectedExam->id]) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-file-text me-1"></i> View Report Card
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($result['subjects']) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>Subject</th>
                                            <th>Full Marks</th>
                                            <th>Marks Obtained</th>
                                            <th>Percentage</th>
                                            <th>Grade</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result['subjects'] as $index => $subject)
                                            <tr class="{{ !$subject['passed'] ? 'table-danger' : '' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $subject['subject']->name }}</strong></td>
                                                <td>{{ number_format($subject['full_marks'], 0) }}</td>
                                                <td>{{ number_format($subject['marks_obtained'], 0) }}</td>
                                                <td>{{ $subject['percentage'] }}%</td>
                                                <td>
                                                    <span class="badge bg-{{ $subject['grade'] == 'F' ? 'danger' : ($subject['grade'] == 'A+' || $subject['grade'] == 'A' ? 'success' : 'primary') }}">
                                                        {{ $subject['grade'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($subject['passed'])
                                                        <span class="badge bg-success">Pass</span>
                                                    @else
                                                        <span class="badge bg-danger">Fail</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th>{{ number_format($result['total_full_marks'], 0) }}</th>
                                            <th>{{ number_format($result['total_marks'], 0) }}</th>
                                            <th>{{ $result['percentage'] }}%</th>
                                            <th><span class="badge bg-primary">{{ $result['grade'] }}</span></th>
                                            <th>
                                                @if($result['result'] == 'Pass')
                                                    <span class="badge bg-success">Pass</span>
                                                @else
                                                    <span class="badge bg-danger">Fail</span>
                                                @endif
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#file-text') }}"></use>
                                </svg>
                                <h6 class="mt-3 text-muted">No Results Found</h6>
                                <p class="text-muted">Your results for this exam have not been published yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif(request('exam_id'))
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#file-text') }}"></use>
                        </svg>
                        <h6 class="mt-3 text-muted">No Results Found</h6>
                        <p class="text-muted">Your results for this exam have not been published yet.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
