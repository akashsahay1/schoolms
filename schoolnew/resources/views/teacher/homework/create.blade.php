@extends('layouts.teacher-portal')

@section('title', 'Assign Homework')
@section('page-title', 'Assign Homework')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.homework.index') }}">Homework</a></li>
<li class="breadcrumb-item active">Assign New</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header pb-0 border-0">
				<h5 class="mb-0">
					<i data-feather="file-plus" style="width: 18px; height: 18px;" class="me-2"></i>Assign New Homework
				</h5>
			</div>
			<div class="card-body">
				<form action="{{ route('teacher.homework.store') }}" method="POST" enctype="multipart/form-data">
					@csrf

					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label">Class <span class="text-danger">*</span></label>
							<select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
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

						<div class="col-md-6">
							<label class="form-label">Section (Optional)</label>
							<select name="section_id" id="section_id" class="form-select @error('section_id') is-invalid @enderror">
								<option value="">All Sections</option>
							</select>
							@error('section_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6">
							<label class="form-label">Subject <span class="text-danger">*</span></label>
							<select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
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

						<div class="col-md-6">
							<label class="form-label">Submission Date <span class="text-danger">*</span></label>
							<input type="date" name="submission_date" class="form-control @error('submission_date') is-invalid @enderror" value="{{ old('submission_date') }}" min="{{ date('Y-m-d') }}" required>
							@error('submission_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-12">
							<label class="form-label">Title <span class="text-danger">*</span></label>
							<input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Homework title" required>
							@error('title')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-12">
							<label class="form-label">Description <span class="text-danger">*</span></label>
							<textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Describe the homework in detail..." required>{{ old('description') }}</textarea>
							@error('description')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-12">
							<label class="form-label">Attachment (Optional)</label>
							<input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
							<small class="text-muted">Max file size: 10MB. Upload study materials, worksheets, etc.</small>
							@error('attachment')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-12 mt-4">
							<button type="submit" class="btn btn-primary">
								<i data-feather="check" style="width: 14px; height: 14px;"></i> Assign Homework
							</button>
							<a href="{{ route('teacher.homework.index') }}" class="btn btn-secondary">Cancel</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card">
			<div class="card-header pb-0 border-0">
				<h6 class="mb-0">
					<i data-feather="help-circle" style="width: 16px; height: 16px;" class="me-2"></i>Tips
				</h6>
			</div>
			<div class="card-body">
				<ul class="list-unstyled mb-0">
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Keep the title clear and concise
					</li>
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Provide detailed instructions
					</li>
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Set a reasonable deadline
					</li>
					<li class="mb-0">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Attach reference materials if needed
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery('#class_id').on('change', function() {
		var classId = jQuery(this).val();
		if (classId) {
			jQuery.get('{{ url("teacher/homework/sections") }}/' + classId, function(data) {
				var options = '<option value="">All Sections</option>';
				jQuery.each(data, function(i, section) {
					options += '<option value="' + section.id + '">' + section.name + '</option>';
				});
				jQuery('#section_id').html(options);
			});
		} else {
			jQuery('#section_id').html('<option value="">All Sections</option>');
		}
	});
});
</script>
@endpush
@endsection
