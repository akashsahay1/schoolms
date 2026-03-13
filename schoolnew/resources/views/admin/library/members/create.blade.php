@extends('layouts.app')

@section('title', 'Add Library Member')

@section('page-title', 'Add Library Member')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.library.members.index') }}">Library Members</a></li>
	<li class="breadcrumb-item active">Add</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<form action="{{ route('admin.library.members.store') }}" method="POST">
			@csrf

			<div class="card">
				<div class="card-header">
					<h5>Member Information</h5>
				</div>
				<div class="card-body">
					<div class="row mb-3">
						<div class="col-md-6">
							<label for="member_type" class="form-label">Member Type <span class="text-danger">*</span></label>
							<select class="form-select @error('member_type') is-invalid @enderror" id="member_type" name="member_type" required>
								<option value="">Select Type</option>
								<option value="student" {{ old('member_type') == 'student' ? 'selected' : '' }}>Student</option>
								<option value="staff" {{ old('member_type') == 'staff' ? 'selected' : '' }}>Staff</option>
							</select>
							@error('member_type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="member_id_ref" class="form-label">Select Member <span class="text-danger">*</span></label>
							<select class="form-select @error('member_id_ref') is-invalid @enderror" id="member_id_ref" name="member_id_ref" required>
								<option value="">Select Member Type First</option>
							</select>
							@error('member_id_ref')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row mb-3">
						<div class="col-md-4">
							<label for="membership_start" class="form-label">Membership Start Date <span class="text-danger">*</span></label>
							<input type="date" class="form-control @error('membership_start') is-invalid @enderror" id="membership_start" name="membership_start" value="{{ old('membership_start', date('Y-m-d')) }}" required>
							@error('membership_start')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="membership_end" class="form-label">Membership End Date</label>
							<input type="date" class="form-control @error('membership_end') is-invalid @enderror" id="membership_end" name="membership_end" value="{{ old('membership_end') }}">
							<small class="text-muted">Leave empty for lifetime membership</small>
							@error('membership_end')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="max_books_allowed" class="form-label">Max Books Allowed <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('max_books_allowed') is-invalid @enderror" id="max_books_allowed" name="max_books_allowed" value="{{ old('max_books_allowed', 3) }}" min="1" max="20" required>
							@error('max_books_allowed')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row mb-3">
						<div class="col-md-12">
							<label for="notes" class="form-label">Notes</label>
							<textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="500">{{ old('notes') }}</textarea>
							@error('notes')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.library.members.index') }}" class="btn btn-secondary">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Create Membership
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	const students = @json($students);
	const staff = @json($staff);

	jQuery('#member_type').on('change', function() {
		const type = jQuery(this).val();
		const memberSelect = jQuery('#member_id_ref');

		memberSelect.empty();
		memberSelect.append('<option value="">Select Member</option>');

		if (type === 'student') {
			students.forEach(function(student) {
				const name = student.first_name + ' ' + student.last_name + ' (' + (student.admission_no || 'N/A') + ')';
				memberSelect.append(`<option value="${student.id}">${name}</option>`);
			});
		} else if (type === 'staff') {
			staff.forEach(function(s) {
				const name = s.first_name + ' ' + s.last_name + ' (' + (s.staff_id || 'N/A') + ')';
				memberSelect.append(`<option value="${s.id}">${name}</option>`);
			});
		}
	});

	// Trigger change if old value exists
	@if(old('member_type'))
		jQuery('#member_type').trigger('change');
		@if(old('member_id_ref'))
			jQuery('#member_id_ref').val('{{ old('member_id_ref') }}');
		@endif
	@endif

	if (typeof feather !== 'undefined') {
		feather.replace();
	}
});
</script>
@endpush
