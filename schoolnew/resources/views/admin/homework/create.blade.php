@extends('layouts.app')

@section('title', 'Assign Homework')

@section('page-title', 'Assign Homework')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.homework.index') }}">Homework</a></li>
	<li class="breadcrumb-item active">Assign New</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-10 col-lg-8">
		<div class="card">
			<div class="card-header">
				<h5>Homework Details</h5>
			</div>
			<div class="card-body">
				<form action="{{ route('admin.homework.store') }}" method="POST" enctype="multipart/form-data">
					@csrf

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
							<select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
								<option value="">Select Academic Year</option>
								@foreach($academicYears as $year)
									<option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
								@endforeach
							</select>
							@error('academic_year_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
							<select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
								<option value="">Select Class</option>
								@foreach($classes as $class)
									<option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
								@endforeach
							</select>
							@error('class_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="section_id" class="form-label">Section</label>
							<select class="form-select @error('section_id') is-invalid @enderror" id="section_id" name="section_id">
								<option value="">All Sections</option>
							</select>
							@error('section_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
							<select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
								<option value="">Select Subject</option>
								@foreach($subjects as $subject)
									<option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
								@endforeach
							</select>
							@error('subject_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="mb-3">
						<label for="title" class="form-label">Title <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
						@error('title')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Description <span class="text-danger">*</span></label>
						<textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
						@error('description')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label for="homework_date" class="form-label">Homework Date <span class="text-danger">*</span></label>
							<input type="date" class="form-control @error('homework_date') is-invalid @enderror" id="homework_date" name="homework_date" value="{{ old('homework_date', date('Y-m-d')) }}" required>
							@error('homework_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-4 mb-3">
							<label for="submission_date" class="form-label">Submission Date <span class="text-danger">*</span></label>
							<input type="date" class="form-control @error('submission_date') is-invalid @enderror" id="submission_date" name="submission_date" value="{{ old('submission_date') }}" required>
							@error('submission_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-4 mb-3">
							<label for="max_marks" class="form-label">Max Marks</label>
							<input type="number" class="form-control @error('max_marks') is-invalid @enderror" id="max_marks" name="max_marks" value="{{ old('max_marks', 0) }}" min="0">
							@error('max_marks')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="mb-3">
						<label for="attachment" class="form-label">Attachment</label>
						<input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
						@error('attachment')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<small class="text-muted">Max 5MB. Supported: PDF, DOC, DOCX, JPG, PNG</small>
					</div>

					<div class="mb-3">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
							<label class="form-check-label" for="is_active">Active</label>
						</div>
					</div>

					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.homework.index') }}" class="btn btn-light">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Assign Homework
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
jQuery(document).ready(function() {
	jQuery('#class_id').on('change', function() {
		var classId = jQuery(this).val();
		if (classId) {
			jQuery.ajax({
				url: '/admin/homework/sections/' + classId,
				type: 'GET',
				success: function(data) {
					jQuery('#section_id').empty().append('<option value="">All Sections</option>');
					jQuery.each(data, function(key, section) {
						jQuery('#section_id').append('<option value="'+ section.id +'">'+ section.name +'</option>');
					});
				}
			});
		}
	});
});
</script>
@endsection
