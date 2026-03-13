@extends('layouts.app')

@section('title', 'Edit Library Member')

@section('page-title', 'Edit Library Member')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.library.members.index') }}">Library Members</a></li>
	<li class="breadcrumb-item active">Edit</li>
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

		<form action="{{ route('admin.library.members.update', $member) }}" method="POST">
			@csrf
			@method('PUT')

			<div class="card">
				<div class="card-header">
					<h5>Member Information</h5>
				</div>
				<div class="card-body">
					<div class="row mb-4">
						<div class="col-md-6">
							<div class="d-flex align-items-center">
								@if($member->memberable)
									<img src="{{ $member->memberable->photo_url ?? asset('assets/images/user/user.png') }}" alt="Photo" class="rounded-circle me-3" width="64" height="64">
									<div>
										<h5 class="mb-1">{{ $member->member_name }}</h5>
										<span class="badge bg-light-{{ $member->member_type === 'Student' ? 'info' : 'primary' }}">{{ $member->member_type }}</span>
										<span class="text-muted ms-2">{{ $member->member_id }}</span>
									</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row mb-3">
						<div class="col-md-4">
							<label for="membership_start" class="form-label">Membership Start Date <span class="text-danger">*</span></label>
							<input type="date" class="form-control @error('membership_start') is-invalid @enderror" id="membership_start" name="membership_start" value="{{ old('membership_start', $member->membership_start->format('Y-m-d')) }}" required>
							@error('membership_start')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="membership_end" class="form-label">Membership End Date</label>
							<input type="date" class="form-control @error('membership_end') is-invalid @enderror" id="membership_end" name="membership_end" value="{{ old('membership_end', $member->membership_end?->format('Y-m-d')) }}">
							<small class="text-muted">Leave empty for lifetime membership</small>
							@error('membership_end')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="status" class="form-label">Status <span class="text-danger">*</span></label>
							<select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
								<option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Active</option>
								<option value="expired" {{ old('status', $member->status) == 'expired' ? 'selected' : '' }}>Expired</option>
								<option value="suspended" {{ old('status', $member->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
							</select>
							@error('status')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row mb-3">
						<div class="col-md-4">
							<label for="max_books_allowed" class="form-label">Max Books Allowed <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('max_books_allowed') is-invalid @enderror" id="max_books_allowed" name="max_books_allowed" value="{{ old('max_books_allowed', $member->max_books_allowed) }}" min="1" max="20" required>
							@error('max_books_allowed')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label class="form-label">Current Books</label>
							<input type="text" class="form-control" value="{{ $member->current_books_count }}" readonly>
						</div>
						<div class="col-md-4">
							<label class="form-label">Outstanding Fines</label>
							<input type="text" class="form-control" value="{{ number_format($member->outstanding_fines, 2) }}" readonly>
						</div>
					</div>

					<div class="row mb-3">
						<div class="col-md-12">
							<label for="notes" class="form-label">Notes</label>
							<textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="500">{{ old('notes', $member->notes) }}</textarea>
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
							<i data-feather="save" class="me-1"></i> Update Membership
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
	if (typeof feather !== 'undefined') {
		feather.replace();
	}
});
</script>
@endpush
