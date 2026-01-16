@extends('layouts.app')

@section('title', 'Promotion Rules')
@section('page-title', 'Promotion Rules')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
    <li class="breadcrumb-item active">Rules</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Add Rule Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Add/Update Promotion Rule</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.promotions.rules.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_active ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Minimum Attendance % <span class="text-danger">*</span></label>
                            <input type="number" name="min_attendance_percentage" class="form-control @error('min_attendance_percentage') is-invalid @enderror" value="{{ old('min_attendance_percentage', 75) }}" min="0" max="100" step="0.01" required>
                            @error('min_attendance_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Minimum Marks % <span class="text-danger">*</span></label>
                            <input type="number" name="min_marks_percentage" class="form-control @error('min_marks_percentage') is-invalid @enderror" value="{{ old('min_marks_percentage', 33) }}" min="0" max="100" step="0.01" required>
                            @error('min_marks_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="consider_attendance" class="form-check-input" id="considerAttendance" {{ old('consider_attendance', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="considerAttendance">Consider Attendance for Promotion</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="consider_marks" class="form-check-input" id="considerMarks" {{ old('consider_marks', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="considerMarks">Consider Marks for Promotion</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="auto_promote" class="form-check-input" id="autoPromote" {{ old('auto_promote') ? 'checked' : '' }}>
                                <label class="form-check-label" for="autoPromote">Enable Auto Promotion</label>
                            </div>
                            <small class="text-muted">If enabled, eligible students will be auto-promoted</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-save me-2"></i> Save Rule
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Rules List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Existing Promotion Rules</h5>
                </div>
                <div class="card-body">
                    @if($rules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Academic Year</th>
                                        <th>Class</th>
                                        <th>Min Attendance</th>
                                        <th>Min Marks</th>
                                        <th>Criteria</th>
                                        <th>Auto</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rules as $rule)
                                        <tr>
                                            <td>{{ $rule->academicYear->name ?? '-' }}</td>
                                            <td>{{ $rule->schoolClass->name ?? '-' }}</td>
                                            <td>{{ $rule->min_attendance_percentage }}%</td>
                                            <td>{{ $rule->min_marks_percentage }}%</td>
                                            <td>
                                                @if($rule->consider_attendance)
                                                    <span class="badge bg-info">Attendance</span>
                                                @endif
                                                @if($rule->consider_marks)
                                                    <span class="badge bg-primary">Marks</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rule->auto_promote)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.promotions.rules.delete', $rule) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-confirm" data-name="{{ $rule->schoolClass->name ?? 'this rule' }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#settings') }}"></use>
                            </svg>
                            <h6 class="mt-3 text-muted">No Rules Defined</h6>
                            <p class="text-muted">Add promotion rules using the form on the left.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
