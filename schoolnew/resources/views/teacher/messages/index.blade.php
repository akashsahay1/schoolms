@extends('layouts.teacher-portal')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('breadcrumb')
<li class="breadcrumb-item active">Messages</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 mb-4">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="mb-0">
				<i data-feather="mail" style="width: 18px; height: 18px;" class="me-2"></i>Inbox
			</h5>
			<a href="{{ route('teacher.messages.compose') }}" class="btn btn-primary">
				<i data-feather="edit" style="width: 14px; height: 14px;"></i> Compose Message
			</a>
		</div>
	</div>

	<div class="col-12">
		<div class="card">
			<div class="card-body">
				@if($messages->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($messages as $message)
							<a href="{{ route('teacher.messages.show', $message) }}" class="list-group-item list-group-item-action {{ !$message->is_read && $message->recipient_id == auth()->id() ? 'bg-light' : '' }}">
								<div class="d-flex justify-content-between align-items-start">
									<div>
										<div class="d-flex align-items-center mb-1">
											@if(!$message->is_read && $message->recipient_id == auth()->id())
												<span class="badge bg-primary me-2">New</span>
											@endif
											<strong>{{ $message->subject }}</strong>
										</div>
										<p class="mb-1 text-muted small">
											@if($message->sender_id == auth()->id())
												<span class="text-primary">To:</span> {{ $message->recipient->name ?? 'Unknown' }}
											@else
												<span class="text-success">From:</span> {{ $message->sender->name ?? 'Unknown' }}
											@endif
										</p>
										<p class="mb-0 text-muted small">{{ Str::limit($message->message, 80) }}</p>
									</div>
									<small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
								</div>
							</a>
						@endforeach
					</div>
					<div class="mt-4">
						{{ $messages->links() }}
					</div>
				@else
					<div class="text-center py-5">
						<i data-feather="inbox" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
						<h5 class="text-muted">No Messages</h5>
						<p class="text-muted mb-3">Your inbox is empty.</p>
						<a href="{{ route('teacher.messages.compose') }}" class="btn btn-primary">Compose Message</a>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
