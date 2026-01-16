@extends('layouts.app')

@section('title', 'Issue History Report')

@section('page-title', 'Book Issue History Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.library.reports.index') }}">Library Reports</a></li>
    <li class="breadcrumb-item active">Issue History</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.library.reports.issues') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">From Date <span class="text-danger">*</span></label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date <span class="text-danger">*</span></label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="filter" class="me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.library.reports.export', ['type' => 'issues', 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="btn btn-success">
                            <i data-feather="download" class="me-1"></i> Export CSV
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white">Total Issued</h6>
                    <h3 class="mb-0">{{ $summary['total_issued'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white">Total Returned</h6>
                    <h3 class="mb-0">{{ $summary['total_returned'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="text-white">Fine Collected</h6>
                    <h3 class="mb-0">{{ number_format($summary['total_fine'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="text-white">Overdue</h6>
                    <h3 class="mb-0">{{ $summary['overdue_count'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header pb-0">
            <h5>Issue Records ({{ $fromDate }} to {{ $toDate }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Book</th>
                            <th>Student</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Fine</th>
                            <th>Status</th>
                            <th>Issued By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issues as $issue)
                            <tr>
                                <td>{{ $issues->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $issue->book->title ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $issue->book->author ?? '' }}</small>
                                </td>
                                <td>{{ $issue->student->full_name ?? 'N/A' }}</td>
                                <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                <td>{{ $issue->due_date->format('d M Y') }}</td>
                                <td>{{ $issue->return_date ? $issue->return_date->format('d M Y') : '-' }}</td>
                                <td>{{ $issue->fine_amount > 0 ? number_format($issue->fine_amount, 2) : '-' }}</td>
                                <td>
                                    @if($issue->status === 'issued')
                                        @if($issue->is_overdue)
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-warning">Issued</span>
                                        @endif
                                    @else
                                        <span class="badge bg-success">Returned</span>
                                    @endif
                                </td>
                                <td>{{ $issue->issuedBy->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <p class="text-muted">No records found for the selected period.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($issues->hasPages())
                <div class="mt-3">{{ $issues->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
