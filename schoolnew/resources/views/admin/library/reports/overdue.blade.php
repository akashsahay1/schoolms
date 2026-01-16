@extends('layouts.app')

@section('title', 'Overdue Books Report')

@section('page-title', 'Overdue Books Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.library.reports.index') }}">Library Reports</a></li>
    <li class="breadcrumb-item active">Overdue Books</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Summary Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="text-white">Total Overdue Books</h5>
                    <h2 class="mb-0">{{ $overdueIssues->total() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="text-white">Total Pending Fine</h5>
                    <h2 class="mb-0">{{ number_format($totalPendingFine, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.library.reports.export', ['type' => 'overdue']) }}" class="btn btn-outline-success w-100">
                        <i data-feather="download" class="me-2"></i> Export to CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Overdue Books List</h5>
                <form action="{{ route('admin.library.reports.overdue') }}" method="GET" class="d-flex gap-2">
                    <select name="student_id" class="form-select" style="width: 200px;">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="bg-danger text-white">
                        <tr>
                            <th>#</th>
                            <th>Book</th>
                            <th>Student</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Overdue Days</th>
                            <th>Calculated Fine</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overdueIssues as $issue)
                            <tr>
                                <td>{{ $overdueIssues->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $issue->book->title ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">ISBN: {{ $issue->book->isbn ?? '-' }}</small>
                                </td>
                                <td>
                                    {{ $issue->student->full_name ?? 'N/A' }}
                                    <br><small class="text-muted">{{ $issue->student->admission_no ?? '' }}</small>
                                </td>
                                <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                <td class="text-danger">{{ $issue->due_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $issue->overdue_days }} days</span>
                                </td>
                                <td class="text-danger fw-bold">{{ number_format($issue->calculated_fine, 2) }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success return-book-btn"
                                        data-issue-id="{{ $issue->id }}"
                                        data-book-title="{{ $issue->book->title }}"
                                        data-student-name="{{ $issue->student->full_name }}"
                                        data-due-date="{{ $issue->due_date->format('Y-m-d') }}"
                                        data-calculated-fine="{{ $issue->calculated_fine }}">
                                        <i data-feather="corner-down-left" style="width: 14px; height: 14px;"></i> Return
                                    </button>
                                    <form id="return-form-{{ $issue->id }}" action="{{ route('admin.library.issue.return', $issue) }}" method="POST" class="d-none">
                                        @csrf
                                        <input type="hidden" name="return_date" id="return-date-{{ $issue->id }}">
                                        <input type="hidden" name="fine_amount" id="fine-amount-{{ $issue->id }}">
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i data-feather="check-circle" style="width: 48px; height: 48px;"></i>
                                        <p class="mt-2 mb-0">No overdue books! All books returned on time.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($overdueIssues->hasPages())
                <div class="mt-3">{{ $overdueIssues->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    jQuery('.return-book-btn').on('click', function() {
        var issueId = jQuery(this).data('issue-id');
        var bookTitle = jQuery(this).data('book-title');
        var studentName = jQuery(this).data('student-name');
        var dueDate = jQuery(this).data('due-date');
        var calculatedFine = jQuery(this).data('calculated-fine');
        var today = new Date().toISOString().split('T')[0];

        Swal.fire({
            title: 'Return Book',
            html: `
                <div class="text-start mb-3">
                    <p class="mb-1"><strong>Book:</strong> ${bookTitle}</p>
                    <p class="mb-0"><strong>Student:</strong> ${studentName}</p>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Return Date <span class="text-danger">*</span></label>
                    <input type="date" id="swal-return-date" class="form-control" value="${today}" required>
                </div>
                <div class="text-start">
                    <label class="form-label">Fine Amount</label>
                    <input type="number" id="swal-fine-amount" class="form-control" value="${calculatedFine}" min="0" step="0.01">
                    <small class="text-muted">Auto-calculated based on overdue days. Due date was: ${dueDate}</small>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Confirm Return',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const returnDate = document.getElementById('swal-return-date').value;
                const fineAmount = document.getElementById('swal-fine-amount').value;

                if (!returnDate) {
                    Swal.showValidationMessage('Please enter the return date');
                    return false;
                }

                return { returnDate: returnDate, fineAmount: fineAmount || 0 };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery('#return-date-' + issueId).val(result.value.returnDate);
                jQuery('#fine-amount-' + issueId).val(result.value.fineAmount);
                jQuery('#return-form-' + issueId).submit();
            }
        });
    });
});
</script>
@endpush
