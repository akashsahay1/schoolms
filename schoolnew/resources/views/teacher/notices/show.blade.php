@extends('layouts.teacher-portal')

@section('title', $notice->title)
@section('page-title', 'Notice Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.notices') }}">Notices</a></li>
<li class="breadcrumb-item active">View Notice</li>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-start mb-4">
					<div>
						<h4 class="mb-2">{{ $notice->title }}</h4>
						<div class="text-muted">
							<span class="me-3">
								<i data-feather="calendar" style="width: 14px; height: 14px;"></i>
								{{ $notice->publish_date->format('M d, Y') }}
							</span>
							@if($notice->priority)
								<span class="badge bg-{{ $notice->priority == 'high' ? 'danger' : ($notice->priority == 'medium' ? 'warning' : 'info') }}">
									{{ ucfirst($notice->priority) }} Priority
								</span>
							@endif
						</div>
					</div>
				</div>
				<hr>
				<div class="notice-content">
					{!! $notice->content !!}
				</div>
				@if($notice->attachment)
					<hr>
					<div class="mt-3">
						<strong>Attachment:</strong>
						<a href="{{ asset('storage/' . $notice->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm ms-2">
							<i data-feather="download" style="width: 14px; height: 14px;"></i> Download
						</a>
					</div>
				@endif
			</div>
			<div class="card-footer">
				<a href="{{ route('teacher.notices') }}" class="btn btn-secondary">
					<i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Notices
				</a>
			</div>
		</div>
	</div>
</div>
@endsection
