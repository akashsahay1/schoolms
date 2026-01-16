@extends('layouts.app')

@section('title', 'Fine Collection Report')

@section('page-title', 'Library Fine Collection Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.library.reports.index') }}">Library Reports</a></li>
    <li class="breadcrumb-item active">Fine Collection</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.library.reports.fines') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">From Date <span class="text-danger">*</span></label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date <span class="text-danger">*</span></label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}" required>
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
                <div class="col-md-5">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="filter" class="me-1"></i> Generate Report
                        </button>
                        <a href="{{ route('admin.library.reports.export', ['type' => 'fines', 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="btn btn-success">
                            <i data-feather="download" class="me-1"></i> Export CSV
                        </a>
                        <a href="{{ route('admin.library.reports.fines') }}" class="btn btn-light">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white">Total Fine Collected ({{ $fromDate }} to {{ $toDate }})</h6>
                    <h2 class="mb-0">{{ number_format($summary['total_fine_collected'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white">Total Records</h6>
                    <h2 class="mb-0">{{ $summary['total_records'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="text-white">Pending Fine (From Overdue Books)</h6>
                    <h2 class="mb-0">{{ number_format($summary['pending_fine'], 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header pb-0">
            <h5>Fine Collection Records</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Return Date</th>
                            <th>Book</th>
                            <th>Student</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Overdue Days</th>
                            <th>Fine Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fineRecords as $record)
                            <tr>
                                <td>{{ $fineRecords->firstItem() + $loop->index }}</td>
                                <td>{{ $record->return_date->format('d M Y') }}</td>
                                <td>
                                    <strong>{{ $record->book->title ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    {{ $record->student->full_name ?? 'N/A' }}
                                    <br><small class="text-muted">{{ $record->student->admission_no ?? '' }}</small>
                                </td>
                                <td>{{ $record->issue_date->format('d M Y') }}</td>
                                <td>{{ $record->due_date->format('d M Y') }}</td>
                                <td>{{ $record->overdue_days }}</td>
                                <td class="fw-bold text-success">{{ number_format($record->fine_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted">No fine collection records found for the selected period.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($fineRecords->hasPages())
                <div class="mt-3">{{ $fineRecords->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
