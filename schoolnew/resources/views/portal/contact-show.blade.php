@extends('layouts.portal')

@section('title', 'Message Details')
@section('page-title', 'Message Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('portal.contact') }}">Contact School</a></li>
	<li class="breadcrumb-item active">Message</li>
@endsection

@section('content')
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header pb-0 bg-light">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">
							<i data-feather="mail" style="width: 18px; height: 18px;"></i> Message Details
						</h5>
						<span class="badge {{ $message->getStatusBadgeClass() }} py-2 px-3">
							@if($message->status === 'pending')
								<i data-feather="clock" style="width: 14px; height: 14px;"></i>
							@elseif($message->status === 'in_progress')
								<i data-feather="loader" style="width: 14px; height: 14px;"></i>
							@elseif($message->status === 'resolved')
								<i data-feather="check-circle" style="width: 14px; height: 14px;"></i>
							@else
								<i data-feather="x-circle" style="width: 14px; height: 14px;"></i>
							@endif
							{{ $message->getStatusLabel() }}
						</span>
					</div>
				</div>
				<div class="card-body">
					<!-- Message Meta -->
					<div class="row mb-4 g-3">
						<div class="col-md-6">
							<div class="bg-light rounded p-3">
								<div class="d-flex align-items-center">
									<i data-feather="folder" class="text-primary me-2" style="width: 18px; height: 18px;"></i>
									<div>
										<small class="text-muted d-block">Category</small>
										<span class="fw-medium">{{ $message->getCategoryLabel() }}</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="bg-light rounded p-3">
								<div class="d-flex align-items-center">
									<i data-feather="alert-circle" class="text-warning me-2" style="width: 18px; height: 18px;"></i>
									<div>
										<small class="text-muted d-block">Priority</small>
										<span class="badge {{ $message->getPriorityBadgeClass() }}">{{ $message->getPriorityLabel() }}</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Subject -->
					<div class="mb-4">
						<label class="text-muted small d-block mb-1">
							<i data-feather="edit-3" style="width: 12px; height: 12px;"></i> Subject
						</label>
						<h5 class="mb-0">{{ $message->subject }}</h5>
					</div>

					<!-- Your Message -->
					<div class="mb-4">
						<label class="text-muted small d-block mb-2">
							<i data-feather="message-square" style="width: 12px; height: 12px;"></i> Your Message
						</label>
						<div class="bg-light p-4 rounded" style="line-height: 1.8;">
							{{ $message->message }}
						</div>
						<small class="text-muted mt-2 d-block">
							<i data-feather="calendar" style="width: 12px; height: 12px;"></i>
							Sent on {{ $message->created_at->format('F d, Y h:i A') }}
						</small>
					</div>

					<!-- Response Section -->
					@if($message->admin_response)
						<hr>
						<div class="mb-4">
							<label class="text-muted small d-block mb-2">
								<i data-feather="message-circle" style="width: 12px; height: 12px;"></i> School's Response
							</label>
							<div class="bg-success bg-opacity-10 p-4 rounded border-start border-4 border-success" style="line-height: 1.8;">
								{{ $message->admin_response }}
							</div>
							@if($message->responded_at)
								<small class="text-muted mt-2 d-block">
									<i data-feather="check" style="width: 12px; height: 12px;"></i>
									Responded on {{ $message->responded_at->format('F d, Y h:i A') }}
								</small>
							@endif
						</div>
					@else
						<div class="alert bg-light-info border-0 py-3">
							<div class="d-flex align-items-center">
								<i data-feather="clock" class="text-info me-3" style="width: 24px; height: 24px;"></i>
								<div>
									<strong class="d-block">Awaiting Response</strong>
									<small class="text-muted">Your message is being reviewed. We will respond soon.</small>
								</div>
							</div>
						</div>
					@endif

					<hr>

					<a href="{{ route('portal.contact') }}" class="btn btn-outline-secondary">
						<i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Messages
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
