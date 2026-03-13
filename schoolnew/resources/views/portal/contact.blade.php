@extends('layouts.portal')

@section('title', 'Contact School')
@section('page-title', 'Contact School')

@section('breadcrumb')
	<li class="breadcrumb-item active">Contact School</li>
@endsection

@section('content')
<div class="container-fluid">
	<!-- Help Tip -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="help-tip">
				<i data-feather="info" class="me-2 text-primary" style="width: 18px; height: 18px;"></i>
				<strong>Contact Us:</strong> Have a question or concern? Send a message to the school administration. You can track the status of your messages and view responses here.
			</div>
		</div>
	</div>

	@if(session('success'))
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<i data-feather="check-circle" style="width: 16px; height: 16px;"></i>
			{{ session('success') }}
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	@endif

	<div class="row">
		<!-- Contact Form -->
		<div class="col-xl-6 mb-4">
			<div class="card h-100">
				<div class="card-header bg-primary" style="padding: 15px 20px;">
					<h5 class="text-white mb-0">
						<i data-feather="send" style="width: 18px; height: 18px;"></i> Send a Message
					</h5>
				</div>
				<div class="card-body">
					@if($errors->any())
						<div class="alert alert-danger">
							<ul class="mb-0">
								@foreach($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form action="{{ route('portal.contact.store') }}" method="POST">
						@csrf

						<div class="mb-3">
							<label class="form-label">
								<i data-feather="folder" style="width: 14px; height: 14px;"></i> Category <span class="text-danger">*</span>
							</label>
							<select name="category" class="form-select" required>
								<option value="">Select a category</option>
								@foreach($categories as $key => $label)
									<option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
							<small class="text-muted">Choose the category that best describes your inquiry</small>
						</div>

						<div class="mb-3">
							<label class="form-label">
								<i data-feather="alert-circle" style="width: 14px; height: 14px;"></i> Priority <span class="text-danger">*</span>
							</label>
							<div class="d-flex gap-3">
								<div class="form-check">
									<input class="form-check-input" type="radio" name="priority" id="priority-low" value="low" {{ old('priority') == 'low' ? 'checked' : '' }}>
									<label class="form-check-label" for="priority-low">
										<span class="badge badge-light-success">Low</span>
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="priority" id="priority-medium" value="medium" {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }}>
									<label class="form-check-label" for="priority-medium">
										<span class="badge badge-light-warning">Medium</span>
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="priority" id="priority-high" value="high" {{ old('priority') == 'high' ? 'checked' : '' }}>
									<label class="form-check-label" for="priority-high">
										<span class="badge badge-light-danger">High</span>
									</label>
								</div>
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label">
								<i data-feather="edit-3" style="width: 14px; height: 14px;"></i> Subject <span class="text-danger">*</span>
							</label>
							<input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required placeholder="Brief subject of your message">
						</div>

						<div class="mb-4">
							<label class="form-label">
								<i data-feather="message-square" style="width: 14px; height: 14px;"></i> Message <span class="text-danger">*</span>
							</label>
							<textarea name="message" class="form-control" rows="5" required placeholder="Please describe your query or concern in detail...">{{ old('message') }}</textarea>
						</div>

						<button type="submit" class="btn btn-primary w-100">
							<i data-feather="send" style="width: 14px; height: 14px;"></i> Send Message
						</button>
					</form>
				</div>
			</div>
		</div>

		<!-- Previous Messages -->
		<div class="col-xl-6 mb-4">
			<div class="card h-100">
				<div class="card-header pb-0">
					<h5 class="mb-0">
						<i data-feather="inbox" style="width: 18px; height: 18px;"></i> My Messages
					</h5>
				</div>
				<div class="card-body">
					@if($messages->count() > 0)
						<ul class="list-group list-group-flush">
							@foreach($messages as $message)
								<li class="list-group-item px-0 py-3">
									<div class="d-flex justify-content-between align-items-start">
										<div class="flex-grow-1">
											<div class="mb-2">
												<span class="badge {{ $message->getStatusBadgeClass() }}">
													@if($message->status === 'pending')
														<i data-feather="clock" style="width: 12px; height: 12px;"></i>
													@elseif($message->status === 'in_progress')
														<i data-feather="loader" style="width: 12px; height: 12px;"></i>
													@elseif($message->status === 'resolved')
														<i data-feather="check-circle" style="width: 12px; height: 12px;"></i>
													@else
														<i data-feather="x-circle" style="width: 12px; height: 12px;"></i>
													@endif
													{{ $message->getStatusLabel() }}
												</span>
												<span class="badge {{ $message->getPriorityBadgeClass() }} ms-1">
													{{ $message->getPriorityLabel() }}
												</span>
											</div>
											<h6 class="mb-1">{{ $message->subject }}</h6>
											<small class="text-muted">
												<i data-feather="calendar" style="width: 12px; height: 12px;"></i>
												{{ $message->created_at->format('M d, Y h:i A') }}
											</small>
										</div>
										<a href="{{ route('portal.contact.show', $message) }}" class="btn btn-sm btn-outline-primary">
											<i data-feather="eye" style="width: 14px; height: 14px;"></i> View
										</a>
									</div>
								</li>
							@endforeach
						</ul>
						<div class="mt-3">
							{{ $messages->links() }}
						</div>
					@else
						<div class="text-center py-5">
							<i data-feather="mail" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
							<h6 class="text-muted">No Messages Yet</h6>
							<p class="text-muted mb-0">Your sent messages will appear here</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	<!-- Contact Info Cards -->
	<div class="row">
		<div class="col-md-4 mb-3">
			<div class="card info-card h-100">
				<div class="card-body text-center py-4">
					<div class="quick-action-icon bg-light-primary mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
						<i data-feather="phone" class="text-primary" style="width: 24px; height: 24px;"></i>
					</div>
					<h6>Phone</h6>
					<p class="text-muted mb-0">Contact school office</p>
				</div>
			</div>
		</div>
		<div class="col-md-4 mb-3">
			<div class="card info-card h-100">
				<div class="card-body text-center py-4">
					<div class="quick-action-icon bg-light-success mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
						<i data-feather="mail" class="text-success" style="width: 24px; height: 24px;"></i>
					</div>
					<h6>Email</h6>
					<p class="text-muted mb-0">Send us an email</p>
				</div>
			</div>
		</div>
		<div class="col-md-4 mb-3">
			<div class="card info-card h-100">
				<div class="card-body text-center py-4">
					<div class="quick-action-icon bg-light-warning mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
						<i data-feather="clock" class="text-warning" style="width: 24px; height: 24px;"></i>
					</div>
					<h6>Office Hours</h6>
					<p class="text-muted mb-0">Mon - Fri: 8:00 AM - 4:00 PM</p>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
