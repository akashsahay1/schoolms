@extends('layouts.app')

@section('title', 'Marks Entry')

@section('page-title', 'Marks Entry')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Exams</a></li>
    <li class="breadcrumb-item active">Marks Entry</li>
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

        <div class="card">
            <div class="card-header">
                <h5>Marks Entry System</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light-primary alert-dismissible fade show border-0" role="alert">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <svg class="text-primary" style="width: 24px; height: 24px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                            </svg>
                        </div>
                        <div class="flex-grow-1">
                            <strong>How to Enter Marks:</strong> Select exam, class and subject, then click "Load Students" to enter marks. Grades are calculated automatically.
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Select Exam <span class="text-danger">*</span></label>
                        <select class="form-select" name="exam_id" required>
                            <option value="">Choose Exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }} - {{ $exam->academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Select Class <span class="text-danger">*</span></label>
                        <select class="form-select" name="class_id" required>
                            <option value="">Choose Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Select Subject <span class="text-danger">*</span></label>
                        <select class="form-select" name="subject_id" required>
                            <option value="">Choose Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i data-feather="search" class="icon-xs"></i> Load Students
                        </button>
                    </div>
                </form>

                @if($students->isNotEmpty())
                    <div class="card border">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-0">
                                        <i data-feather="edit-3" class="icon-xs text-primary"></i>
                                        {{ $selectedExam->name }} - {{ $selectedClass->name }} - {{ $selectedSubject->name }}
                                    </h6>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge badge-light-info">{{ $students->count() }} Students</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.exams.marks.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="exam_id" value="{{ $selectedExam->id }}">
                                <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                                
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="full_marks" class="form-label">Full Marks <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('full_marks') is-invalid @enderror" 
                                               id="full_marks" 
                                               name="full_marks" 
                                               value="{{ old('full_marks', 100) }}" 
                                               min="1" 
                                               max="1000" 
                                               required>
                                        @error('full_marks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Roll No</th>
                                                <th>Student Name</th>
                                                <th>Marks Obtained <span class="text-danger">*</span></th>
                                                <th>Percentage</th>
                                                <th>Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $student)
                                                @php
                                                    $existingMark = null;
                                                    try {
                                                        $existingMark = \App\Models\ExamMark::where('exam_id', $selectedExam->id)
                                                            ->where('student_id', $student->id)
                                                            ->where('subject_id', $selectedSubject->id)
                                                            ->first();
                                                    } catch (\Exception $e) {
                                                        // Table may not exist yet
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $student->roll_no }}</td>
                                                    <td>
                                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control marks-input @error('marks.'.$student->id) is-invalid @enderror" 
                                                               name="marks[{{ $student->id }}]" 
                                                               value="{{ old('marks.'.$student->id, $existingMark ? $existingMark->marks_obtained : '') }}" 
                                                               min="0" 
                                                               step="0.01" 
                                                               required 
                                                               data-student-id="{{ $student->id }}">
                                                        @error('marks.'.$student->id)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <span class="percentage-display text-muted" data-student-id="{{ $student->id }}">
                                                            @if($existingMark)
                                                                {{ $existingMark->percentage }}%
                                                            @else
                                                                -
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge grade-display" data-student-id="{{ $student->id }}">
                                                            @if($existingMark)
                                                                {{ $existingMark->grade }}
                                                            @else
                                                                -
                                                            @endif
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success">
                                            <i data-feather="save" class="icon-xs"></i> Save All Marks
                                        </button>
                                        <a href="{{ route('admin.exams.marks') }}" class="btn btn-secondary">
                                            <i data-feather="refresh-cw" class="icon-xs"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i data-feather="users" class="icon-lg text-muted mb-3"></i>
                        <h5 class="text-muted">
                            @if(request()->has('exam_id') && request()->has('class_id') && request()->has('subject_id'))
                                No Students Found
                            @else
                                Select Exam, Class, and Subject
                            @endif
                        </h5>
                        <p class="text-muted">
                            @if(request()->has('exam_id') && request()->has('class_id') && request()->has('subject_id'))
                                No active students found for the selected class.
                            @else
                                Choose the exam, class, and subject to start entering marks for students.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fullMarksInput = document.getElementById('full_marks');
    const marksInputs = document.querySelectorAll('.marks-input');

    function calculateGrade(percentage) {
        if (percentage >= 90) return { grade: 'A+', class: 'bg-success' };
        if (percentage >= 80) return { grade: 'A', class: 'bg-success' };
        if (percentage >= 70) return { grade: 'B+', class: 'bg-info' };
        if (percentage >= 60) return { grade: 'B', class: 'bg-info' };
        if (percentage >= 50) return { grade: 'C+', class: 'bg-warning' };
        if (percentage >= 40) return { grade: 'C', class: 'bg-warning' };
        if (percentage >= 33) return { grade: 'D', class: 'bg-secondary' };
        return { grade: 'F', class: 'bg-danger' };
    }

    function updateCalculations() {
        const fullMarks = parseFloat(fullMarksInput.value) || 100;
        
        marksInputs.forEach(input => {
            const studentId = input.dataset.studentId;
            const marksObtained = parseFloat(input.value) || 0;
            const percentage = fullMarks > 0 ? (marksObtained / fullMarks * 100).toFixed(2) : 0;
            const gradeInfo = calculateGrade(percentage);
            
            const percentageSpan = document.querySelector(`.percentage-display[data-student-id="${studentId}"]`);
            const gradeSpan = document.querySelector(`.grade-display[data-student-id="${studentId}"]`);
            
            if (percentageSpan) {
                percentageSpan.textContent = percentage + '%';
            }
            
            if (gradeSpan) {
                gradeSpan.textContent = gradeInfo.grade;
                gradeSpan.className = `badge grade-display ${gradeInfo.class}`;
            }
            
            // Update validation
            if (marksObtained > fullMarks) {
                input.setCustomValidity(`Marks cannot exceed ${fullMarks}`);
            } else {
                input.setCustomValidity('');
            }
        });
    }

    // Update max attribute when full marks changes
    fullMarksInput.addEventListener('input', function() {
        const fullMarks = this.value;
        marksInputs.forEach(input => {
            input.setAttribute('max', fullMarks);
        });
        updateCalculations();
    });

    // Update calculations when marks change
    marksInputs.forEach(input => {
        input.addEventListener('input', updateCalculations);
    });

    // Initial calculation
    updateCalculations();
});
</script>
@endsection