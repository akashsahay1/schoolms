@extends('layouts.portal')

@section('title', 'Exams')
@section('page-title', 'My Exams')

@section('breadcrumb')
    <li class="breadcrumb-item active">Exams</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-md-4 col-sm-6">
            <div class="card small-widget">
                <div class="card-body primary">
                    <span class="f-light">Total Exams</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $exams->count() }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#file-text') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card small-widget">
                <div class="card-body success">
                    <span class="f-light">Results Published</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $exams->where('has_results', true)->count() }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#check-circle') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card small-widget">
                <div class="card-body warning">
                    <span class="f-light">Upcoming Exams</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $exams->where('status', 'upcoming')->count() }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#clock') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exams List -->
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Examination Schedule</h5>
                </div>
                <div class="card-body">
                    @if($exams->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>#</th>
                                        <th>Exam Name</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Results</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exams as $index => $exam)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $exam->name }}</strong>
                                                @if($exam->description)
                                                    <br><small class="text-muted">{{ Str::limit($exam->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $exam->examType->name ?? '-' }}</td>
                                            <td>
                                                {{ $exam->start_date->format('M d, Y') }}
                                                @if($exam->start_date != $exam->end_date)
                                                    <br><small class="text-muted">to {{ $exam->end_date->format('M d, Y') }}</small>
                                                @endif
                                            </td>
                                            <td>{!! $exam->status_badge !!}</td>
                                            <td>
                                                @if($exam->has_results)
                                                    <span class="badge bg-success">Available</span>
                                                @else
                                                    <span class="badge bg-secondary">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($exam->has_results)
                                                    <a href="{{ route('portal.exams.results', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-eye me-1"></i> View Results
                                                    </a>
                                                    <a href="{{ route('portal.exams.report-card', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa fa-file-text me-1"></i> Report Card
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#file-text') }}"></use>
                            </svg>
                            <h6 class="mt-3 text-muted">No Exams Found</h6>
                            <p class="text-muted">No exams have been published for your class yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
