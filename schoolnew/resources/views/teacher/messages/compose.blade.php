@extends('layouts.teacher-portal')

@section('title', 'Compose Message')
@section('page-title', 'Compose Message')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.messages.index') }}">Messages</a></li>
<li class="breadcrumb-item active">Compose</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header pb-0 border-0">
				<h5 class="mb-0">
					<i data-feather="edit" style="width: 18px; height: 18px;" class="me-2"></i>New Message
				</h5>
			</div>
			<div class="card-body">
				<form action="{{ route('teacher.messages.store') }}" method="POST">
					@csrf

					<div class="mb-3">
						<label class="form-label">To (Student's Parent) <span class="text-danger">*</span></label>
						<select name="recipient_id" class="form-select @error('recipient_id') is-invalid @enderror" required>
							<option value="">Select Recipient</option>
							@foreach($students as $student)
								@if($student->parent && $student->parent->user_id)
									<option value="{{ $student->parent->user_id }}" {{ old('recipient_id') == $student->parent->user_id ? 'selected' : '' }}>
										{{ $student->full_name }}'s Parent
										({{ $student->schoolClass->name ?? '' }} - {{ $student->section->name ?? '' }})
									</option>
								@endif
							@endforeach
						</select>
						@error('recipient_id')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label">Subject <span class="text-danger">*</span></label>
						<input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="Message subject" required>
						@error('subject')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label class="form-label">Message <span class="text-danger">*</span></label>
						<textarea name="message" rows="6" class="form-control @error('message') is-invalid @enderror" placeholder="Type your message here..." required>{{ old('message') }}</textarea>
						@error('message')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mt-4">
						<button type="submit" class="btn btn-primary">
							<i data-feather="send" style="width: 14px; height: 14px;"></i> Send Message
						</button>
						<a href="{{ route('teacher.messages.index') }}" class="btn btn-secondary">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card">
			<div class="card-header pb-0 border-0">
				<h6 class="mb-0">
					<i data-feather="help-circle" style="width: 16px; height: 16px;" class="me-2"></i>Quick Tips
				</h6>
			</div>
			<div class="card-body">
				<ul class="list-unstyled mb-0">
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Be clear and professional
					</li>
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Include relevant student info
					</li>
					<li class="mb-2">
						<i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
						Use a descriptive subject line
					</li>
					<li class="mb-0">
						<i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i>
						Parents will be notified
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection
