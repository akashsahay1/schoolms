@extends('layouts.portal')

@section('title', $notice->title)
@section('page-title', 'Notice Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('portal.notices') }}">Notices</a></li>
	<li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-body">
					<!-- Notice Type & Status -->
					<div class="mb-4 d-flex flex-wrap gap-2">
						<span class="badge {{ $notice->getTypeBadgeClass() }} py-2 px-3">
							@if($notice->type === 'urgent')
								<i data-feather="alert-circle" style="width: 14px; height: 14px;"></i>
							@elseif($notice->type === 'announcement')
								<i data-feather="bell" style="width: 14px; height: 14px;"></i>
							@elseif($notice->type === 'circular')
								<i data-feather="file-text" style="width: 14px; height: 14px;"></i>
							@else
								<i data-feather="info" style="width: 14px; height: 14px;"></i>
							@endif
							{{ $notice->getTypeLabel() }}
						</span>
						@if($notice->isExpired())
							<span class="badge badge-light-danger py-2 px-3">
								<i data-feather="alert-triangle" style="width: 14px; height: 14px;"></i> Expired
							</span>
						@endif
					</div>

					<!-- Notice Title -->
					<h3 class="mb-4">{{ $notice->title }}</h3>

					<!-- Meta Information -->
					<div class="bg-light rounded p-3 mb-4">
						<div class="row">
							<div class="col-md-6">
								<div class="d-flex align-items-center mb-2 mb-md-0">
									<i data-feather="calendar" class="text-primary me-2" style="width: 18px; height: 18px;"></i>
									<div>
										<small class="text-muted d-block">Published</small>
										<span class="fw-medium">{{ $notice->publish_date->format('F d, Y') }}</span>
									</div>
								</div>
							</div>
							@if($notice->expiry_date)
								<div class="col-md-6">
									<div class="d-flex align-items-center">
										<i data-feather="clock" class="text-warning me-2" style="width: 18px; height: 18px;"></i>
										<div>
											<small class="text-muted d-block">Expires</small>
											<span class="fw-medium">{{ $notice->expiry_date->format('F d, Y') }}</span>
										</div>
									</div>
								</div>
							@endif
						</div>
					</div>

					<hr>

					<!-- Notice Content -->
					<div class="notice-content py-3" style="line-height: 1.8;">
						{!! nl2br(e($notice->content)) !!}
					</div>

					<!-- Attachment -->
					@if($notice->attachment)
						<hr>
						<div class="mt-4 p-3 bg-light rounded">
							<h6 class="mb-3">
								<i data-feather="paperclip" style="width: 16px; height: 16px;"></i> Attachment
							</h6>
							<a href="{{ asset('storage/' . $notice->attachment) }}" class="btn btn-primary" target="_blank">
								<i data-feather="download" style="width: 14px; height: 14px;"></i> Download Attachment
							</a>
						</div>
					@endif

					<hr>

					<!-- Back Button -->
					<div class="mt-4">
						<a href="{{ route('portal.notices') }}" class="btn btn-outline-secondary">
							<i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Notices
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
