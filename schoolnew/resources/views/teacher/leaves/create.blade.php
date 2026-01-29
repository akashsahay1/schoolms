@extends('layouts.teacher-portal')

@section('title', 'Apply for Leave')
@section('page-title', 'Apply for Leave')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.leaves.index') }}">My Leave</a></li>
<li class="breadcrumb-item active">Apply for Leave</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header pb-0">
				<h5 class="mb-0">Leave Application Form</h5>
			</div>
			<div class="card-body">
				<form action="{{ route('teacher.leaves.store') }}" method="POST" enctype="multipart/form-data">
					@csrf

					<div class="row g-3">
						<div class="col-md-12">
							<label class="form-label">Leave Type <span class="text-danger">*</span></label>
							<select name="leave_type_id" class="form-select @error('leave_type_id') is-invalid @enderror" required>
								<option value="">Select Leave Type</option>
								@foreach($leaveTypes as $type)
									<option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
										{{ $type->name }}
									</option>
								@endforeach
							</select>
							@error('leave_type_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6">
							<label class="form-label">From Date <span class="text-danger">*</span></label>
							<input type="date" name="from_date" class="form-control @error('from_date') is-invalid @enderror" value="{{ old('from_date') }}" min="{{ date('Y-m-d') }}" required>
							@error('from_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6">
							<label class="form-label">To Date <span class="text-danger">*</span></label>
							<input type="date" name="to_date" class="form-control @error('to_date') is-invalid @enderror" value="{{ old('to_date') }}" min="{{ date('Y-m-d') }}" required>
							@error('to_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-12">
							<label class="form-label">Reason <span class="text-danger">*</span></label>
							<textarea name="reason" rows="4" class="form-control @error('reason') is-invalid @enderror" placeholder="Please provide a reason for your leave request..." required>{{ old('reason') }}</textarea>
							@error('reason')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-12">
							<label class="form-label">Attachment (Optional)</label>
							<input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
							<small class="text-muted">Max file size: 5MB. Supported formats: PDF, DOC, DOCX, JPG, PNG</small>
							@error('attachment')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-12 mt-4">
							<button type="submit" class="btn btn-primary">
								<i data-feather="send" style="width: 14px; height: 14px;"></i> Submit Application
							</button>
							<a href="{{ route('teacher.leaves.index') }}" class="btn btn-secondary">Cancel</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card">
			<div class="card-header pb-0">
				<h6 class="mb-0">Instructions</h6>
			</div>
			<div class="card-body">
				<ul class="list-unstyled mb-0">
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Select the appropriate leave type
					</li>
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Choose dates carefully
					</li>
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Provide a clear reason
					</li>
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Attach supporting documents if required
					</li>
					<li class="mb-0">
						<i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i>
						Your application will be reviewed by admin
					</li>
				</ul>
			</div>
		</div>

		<div class="card mt-3">
			<div class="card-body text-center">
				<a href="{{ route('teacher.leaves.balance') }}" class="btn btn-outline-primary w-100">
					<i data-feather="pie-chart" style="width: 14px; height: 14px;"></i> Check Leave Balance
				</a>
			</div>
		</div>
	</div>
</div>
@endsection
