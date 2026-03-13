@extends('layouts.teacher-portal')

@section('title', $message->subject)
@section('page-title', 'View Message')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.messages.index') }}">Messages</a></li>
<li class="breadcrumb-item active">View Message</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-start mb-4">
					<div>
						<h5 class="mb-1">{{ $message->subject }}</h5>
						<p class="text-muted mb-0">
							@if($message->sender_id == auth()->id())
								<strong>To:</strong> {{ $message->recipient->name ?? 'Unknown' }}
							@else
								<strong>From:</strong> {{ $message->sender->name ?? 'Unknown' }}
							@endif
						</p>
						<small class="text-muted">{{ $message->created_at->format('M d, Y h:i A') }}</small>
					</div>
				</div>

				<hr>

				<div class="message-content py-3">
					{!! nl2br(e($message->message)) !!}
				</div>
			</div>
			<div class="card-footer">
				<a href="{{ route('teacher.messages.index') }}" class="btn btn-secondary">
					<i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Inbox
				</a>
				@if($message->sender_id != auth()->id())
					<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal">
						<i data-feather="corner-up-left" style="width: 14px; height: 14px;"></i> Reply
					</button>
				@endif
			</div>
		</div>
	</div>
</div>

<!-- Reply Modal -->
@if($message->sender_id != auth()->id())
<div class="modal fade" id="replyModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Reply to Message</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<form action="{{ route('teacher.messages.reply', $message) }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label">To</label>
						<input type="text" class="form-control" value="{{ $message->sender->name ?? 'Unknown' }}" readonly>
					</div>
					<div class="mb-3">
						<label class="form-label">Subject</label>
						<input type="text" class="form-control" value="Re: {{ $message->subject }}" readonly>
					</div>
					<div class="mb-3">
						<label class="form-label">Message <span class="text-danger">*</span></label>
						<textarea name="message" rows="5" class="form-control" placeholder="Type your reply..." required></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">
						<i data-feather="send" style="width: 14px; height: 14px;"></i> Send Reply
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif
@endsection
