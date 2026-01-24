@extends('layouts.portal')

@section('title', 'Submitted Homework')
@section('page-title', 'Submitted Homework')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.homework.index') }}">Homework</a></li>
    <li class="breadcrumb-item active">Submitted</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Submitted Homework</h5>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>#</th>
                                        <th>Homework Title</th>
                                        <th>Subject</th>
                                        <th>Submitted Date</th>
                                        <th>Status</th>
                                        <th>Marks</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $index => $submission)
                                        <tr>
                                            <td>{{ $submissions->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $submission->homework->title ?? '-' }}</strong>
                                            </td>
                                            <td>{{ $submission->homework->subject->name ?? '-' }}</td>
                                            <td>{{ $submission->submitted_date ? $submission->submitted_date->format('M d, Y h:i A') : '-' }}</td>
                                            <td>
                                                @if($submission->status == 'evaluated')
                                                    <span class="badge bg-success">Evaluated</span>
                                                @elseif($submission->status == 'submitted')
                                                    <span class="badge bg-info">Submitted</span>
                                                @elseif($submission->status == 'late')
                                                    <span class="badge bg-warning">Late Submission</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($submission->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submission->marks_obtained !== null)
                                                    <strong>{{ $submission->marks_obtained }}</strong> / {{ $submission->homework->max_marks ?? '-' }}
                                                    @php
                                                        $percentage = $submission->homework->max_marks > 0
                                                            ? ($submission->marks_obtained / $submission->homework->max_marks) * 100
                                                            : 0;
                                                    @endphp
                                                    <br><small class="text-{{ $percentage >= 50 ? 'success' : 'danger' }}">
                                                        ({{ number_format($percentage, 1) }}%)
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submission->remarks)
                                                    <span title="{{ $submission->remarks }}">{{ Str::limit($submission->remarks, 30) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('portal.homework.show', $submission->homework) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $submissions->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#file-text') }}"></use>
                            </svg>
                            <h6 class="mt-3 text-muted">No Submissions Found</h6>
                            <p class="text-muted">You haven't submitted any homework yet.</p>
                            <a href="{{ route('portal.homework.pending') }}" class="btn btn-outline-primary">
                                View Pending Homework
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
