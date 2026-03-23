@extends('layouts.app')

@section('title', 'Student-wise Library Report')

@section('page-title', 'Student-wise Library Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.library.reports.index') }}">Library Reports</a></li>
    <li class="breadcrumb-item active">Student-wise</li>
@endsection

@push('styles')
<style>
    .student-cell .name {
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.3;
    }
    .student-cell .admission {
        font-size: 11px;
        color: #95a5a6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Filter Report</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.library.reports.student-wise') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select" id="classFilter">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select" id="studentFilter">
                        <option value="">All Students</option>
                        @foreach($studentList as $s)
                            <option value="{{ $s->id }}" data-class="{{ $s->class_id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->full_name }} ({{ $s->admission_no }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="icon-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.library.reports.student-wise') }}" class="btn btn-outline-secondary" title="Reset">
                            <i class="icon-reload"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Student Library Statistics</h6>
                <span class="text-muted" style="font-size: 13px;">{{ $students->total() }} {{ Str::plural('student', $students->total()) }} found</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 25%;">Student</th>
                            <th style="width: 18%;">Class</th>
                            <th style="width: 14%;" class="text-center">Total Borrowed</th>
                            <th style="width: 14%;" class="text-center">Currently Issued</th>
                            <th style="width: 14%;" class="text-end">Total Fine Paid</th>
                            <th style="width: 10%;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="text-muted">{{ $students->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="student-cell">
                                        <div class="name">{{ $student->full_name }}</div>
                                        <div class="admission">{{ $student->admission_no }}</div>
                                    </div>
                                </td>
                                <td>
                                    {{ $student->schoolClass->name ?? '-' }}
                                    @if($student->section)
                                        <span class="text-muted">({{ $student->section->name }})</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">{{ $student->book_issues_count }}</td>
                                <td class="text-center">
                                    @if($student->current_issues_count > 0)
                                        <span class="badge badge-light-warning px-3 py-1">{{ $student->current_issues_count }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(($student->book_issues_sum_fine_amount ?? 0) > 0)
                                        <span class="fw-bold text-danger">₹{{ number_format($student->book_issues_sum_fine_amount, 2) }}</span>
                                    @else
                                        <span class="text-muted">₹0.00</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.library.reports.issues', ['student_id' => $student->id]) }}" class="square-white" title="View History">
                                        <svg>
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                            <i class="icon-user" style="font-size: 24px; color: #95a5a6;"></i>
                                        </div>
                                        <p class="text-muted mb-0">No students found for selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($students->hasPages())
            <div class="card-footer bg-white">
                {{ $students->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    var allStudentOptions = jQuery('#studentFilter option').clone();

    jQuery('#classFilter').on('change', function() {
        var classId = jQuery(this).val();
        var studentSelect = jQuery('#studentFilter');
        var currentStudent = studentSelect.val();

        studentSelect.html('<option value="">All Students</option>');

        allStudentOptions.each(function() {
            var opt = jQuery(this);
            if (!opt.val()) return;
            if (!classId || opt.data('class') == classId) {
                studentSelect.append(opt.clone());
            }
        });

        // Restore selection if still valid
        if (currentStudent && studentSelect.find('option[value="' + currentStudent + '"]').length) {
            studentSelect.val(currentStudent);
        }
    });

    // Trigger on load if class is pre-selected
    @if(request('class_id'))
        jQuery('#classFilter').trigger('change');
        @if(request('student_id'))
            jQuery('#studentFilter').val('{{ request('student_id') }}');
        @endif
    @endif
});
</script>
@endpush
