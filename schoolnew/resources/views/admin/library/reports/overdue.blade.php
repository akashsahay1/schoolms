@extends('layouts.app')

@section('title', 'Overdue Books Report')

@section('page-title', 'Overdue Books Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.library.reports.index') }}">Library Reports</a></li>
    <li class="breadcrumb-item active">Overdue Books</li>
@endsection

@push('styles')
<style>
    .stat-card-overdue {
        border: none;
        border-radius: 14px;
        overflow: hidden;
        transition: transform 0.15s ease;
    }
    .stat-card-overdue:hover {
        transform: translateY(-2px);
    }
    .stat-card-overdue .card-body {
        padding: 1.5rem;
    }
    .stat-card-overdue .stat-icon-circle {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-card-overdue .stat-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #7f8c8d;
        margin-bottom: 4px;
        font-weight: 500;
    }
    .stat-card-overdue .stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .overdue-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .overdue-pill.severity-high {
        background: rgba(231, 76, 60, 0.1);
        color: #c0392b;
    }
    .overdue-pill.severity-mid {
        background: rgba(230, 126, 34, 0.1);
        color: #d35400;
    }
    .overdue-pill.severity-low {
        background: rgba(241, 196, 15, 0.12);
        color: #c49000;
    }
    .book-cell .book-title {
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.3;
        margin-bottom: 2px;
    }
    .book-cell .book-isbn {
        font-size: 11px;
        color: #95a5a6;
    }
    .student-cell .student-name {
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 2px;
    }
    .student-cell .student-id {
        font-size: 11px;
        color: #95a5a6;
    }
    .btn-return {
        border-radius: 8px;
        font-weight: 500;
        font-size: 12px;
        padding: 6px 14px;
        transition: all 0.2s ease;
        border: 1px solid #27ae60;
        color: #27ae60;
        background: transparent;
    }
    .btn-return:hover {
        background: #27ae60;
        color: #fff;
        transform: scale(1.04);
        box-shadow: 0 3px 10px rgba(39, 174, 96, 0.3);
    }
    .fine-amount {
        font-weight: 700;
        color: #e74c3c;
    }
    .table-overdue thead th {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #5a6c7d;
        background: #f4f6f9;
        border-bottom: 2px solid #e9ecef;
        padding: 12px 10px;
    }
    .table-overdue tbody td {
        padding: 14px 10px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f2f5;
    }
    .table-overdue tbody tr:hover {
        background: #fafbfd;
    }
    .empty-state-wrap {
        padding: 60px 20px;
    }
    .empty-state-wrap .empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        background: rgba(39, 174, 96, 0.08);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card stat-card-overdue shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-circle" style="background: rgba(231, 76, 60, 0.08);">
                            <i class="icon-alert" style="font-size: 22px; color: #e74c3c;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Overdue Books</div>
                            <div class="stat-value text-danger">{{ $overdueIssues->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card-overdue shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-circle" style="background: rgba(243, 156, 18, 0.08);">
                            <span style="font-size: 22px; font-weight: 700; color: #e67e22;">₹</span>
                        </div>
                        <div>
                            <div class="stat-label">Pending Fine</div>
                            <div class="stat-value" style="color: #e67e22;">₹{{ number_format($totalPendingFine, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card-overdue shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-circle" style="background: rgba(52, 152, 219, 0.08);">
                            <i class="icon-user" style="font-size: 22px; color: #3498db;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Students Affected</div>
                            <div class="stat-value text-primary">{{ $overdueIssues->unique('student_id')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter + Table Card -->
    <div class="card shadow-sm border-0" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold" style="font-size: 15px;">Overdue Books List</h6>
                <form action="{{ route('admin.library.reports.overdue') }}" method="GET" class="d-flex gap-2 align-items-center">
                    <select name="student_id" class="form-select" style="width: 220px;">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-filter me-1"></i> Filter
                    </button>
                    @if(request('student_id'))
                        <a href="{{ route('admin.library.reports.overdue') }}" class="btn btn-outline-secondary" title="Reset">
                            <i class="icon-reload"></i>
                        </a>
                    @endif
                    <a href="{{ route('admin.library.reports.export', ['type' => 'overdue']) }}" class="btn btn-outline-success" title="Export Excel">
                        <i class="icon-download me-1"></i> Excel
                    </a>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-overdue mb-0">
                    <thead>
                        <tr>
                            <th style="width: 4%;">#</th>
                            <th style="width: 22%;">Book</th>
                            <th style="width: 17%;">Student</th>
                            <th style="width: 11%;">Issue Date</th>
                            <th style="width: 11%;">Due Date</th>
                            <th style="width: 13%;" class="text-center">Overdue</th>
                            <th style="width: 11%;" class="text-end">Fine</th>
                            <th style="width: 11%;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overdueIssues as $issue)
                            <tr>
                                <td class="text-muted">{{ $overdueIssues->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="book-cell">
                                        <div class="book-title">{{ Str::limit($issue->book->title ?? 'N/A', 32) }}</div>
                                        <div class="book-isbn">ISBN: {{ $issue->book->isbn ?? '-' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-name">{{ $issue->student->full_name ?? 'N/A' }}</div>
                                        <div class="student-id">{{ $issue->student->admission_no ?? '' }}</div>
                                    </div>
                                </td>
                                <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                <td class="text-danger fw-medium">{{ $issue->due_date->format('d M Y') }}</td>
                                <td class="text-center">
                                    @php
                                        $days = $issue->overdue_days;
                                        $severity = $days > 14 ? 'severity-high' : ($days > 7 ? 'severity-mid' : 'severity-low');
                                    @endphp
                                    <span class="overdue-pill {{ $severity }}">
                                        <i class="icon-timer" style="font-size: 12px;"></i>
                                        {{ $days }} {{ $days == 1 ? 'day' : 'days' }}
                                    </span>
                                </td>
                                <td class="text-end fine-amount">₹{{ number_format($issue->calculated_fine, 2) }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn-return return-book-btn" data-issue-id="{{ $issue->id }}" data-book-title="{{ $issue->book->title }}" data-student-name="{{ $issue->student->full_name }}" data-due-date="{{ $issue->due_date->format('Y-m-d') }}" data-calculated-fine="{{ $issue->calculated_fine }}">
                                        <i class="icon-share-alt me-1" style="font-size: 12px;"></i> Return
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
                                <td colspan="8">
                                    <div class="empty-state-wrap text-center">
                                        <div class="empty-icon">
                                            <i class="icon-check" style="font-size: 36px; color: #27ae60;"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1">All Clear!</h5>
                                        <p class="text-muted mb-0" style="font-size: 14px;">No overdue books. All books have been returned on time.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($overdueIssues->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $overdueIssues->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    jQuery(document).on('click', '.return-book-btn', function() {
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
