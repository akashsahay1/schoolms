@extends('layouts.app')

@section('title', 'Add Timetable Entry')

@section('page-title', 'Add Timetable Entry')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.timetable.index') }}">Timetable</a></li>
    <li class="breadcrumb-item active">Add Entry</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 col-lg-6">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5>Add Timetable Entry</h5>
                <p class="mb-0">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.timetable.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" id="class-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', request('class_id')) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" class="form-select @error('section_id') is-invalid @enderror" id="section-select" required>
                                <option value="">Select Section</option>
                                @if($selectedClass)
                                    @foreach($selectedClass->sections as $section)
                                        <option value="{{ $section->id }}" {{ old('section_id', request('section_id')) == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Day <span class="text-danger">*</span></label>
                            <select name="day" class="form-select @error('day') is-invalid @enderror" required>
                                <option value="">Select Day</option>
                                @foreach($days as $key => $name)
                                    <option value="{{ $key }}" {{ old('day', request('day')) == $key ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('day')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Period <span class="text-danger">*</span></label>
                            <select name="period_id" class="form-select @error('period_id') is-invalid @enderror" required>
                                <option value="">Select Period</option>
                                @foreach($periods->where('type', 'class') as $period)
                                    <option value="{{ $period->id }}" {{ old('period_id', request('period_id')) == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }} ({{ $period->start_time->format('g:i A') }} - {{ $period->end_time->format('g:i A') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('period_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" id="subject-select" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->full_name }} {{ $teacher->designation ? '(' . $teacher->designation->name . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Room Number</label>
                            <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror" value="{{ old('room_number') }}" placeholder="e.g., Room 101">
                            @error('room_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes') }}" placeholder="Optional notes">
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.timetable.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Save Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const classesData = @json($classes);

    jQuery(document).ready(function() {
        jQuery('#class-select').on('change', function() {
            var classId = jQuery(this).val();
            var sectionSelect = jQuery('#section-select');
            var subjectSelect = jQuery('#subject-select');

            sectionSelect.html('<option value="">Select Section</option>');
            subjectSelect.html('<option value="">Select Subject</option>');

            if (classId) {
                var selectedClass = classesData.find(c => c.id == classId);
                if (selectedClass && selectedClass.sections) {
                    selectedClass.sections.forEach(function(section) {
                        sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
                    });
                }

                // Load subjects for this class
                jQuery.get('/admin/timetable/subjects/' + classId, function(subjects) {
                    subjects.forEach(function(subject) {
                        subjectSelect.append('<option value="' + subject.id + '">' + subject.name + '</option>');
                    });
                });
            }
        });
    });
</script>
@endpush
