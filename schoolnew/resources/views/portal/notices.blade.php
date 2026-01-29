@extends('layouts.portal')

@section('title', 'Notices')
@section('page-title', 'Notices')

@section('breadcrumb')
	<li class="breadcrumb-item active">Notices</li>
@endsection

@section('content')
<div class="container-fluid">
	<!-- Help Tip -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="help-tip">
				<i data-feather="info" class="me-2 text-primary" style="width: 18px; height: 18px;"></i>
				<strong>School Notices:</strong> Stay updated with important announcements, circulars, and alerts from the school. Use the filter to view specific types of notices.
			</div>
		</div>
	</div>

	<!-- Filter -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card">
				<div class="card-body py-3">
					<form method="GET" action="{{ route('portal.notices') }}" class="row g-3 align-items-end">
						<div class="col-md-4 col-6">
							<label class="form-label">
								<i data-feather="filter" style="width: 14px; height: 14px;"></i> Filter by Type
							</label>
							<select name="type" class="form-select">
								<option value="">All Types</option>
								@foreach(\App\Models\Notice::TYPES as $key => $label)
									<option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-4 col-6">
							<button type="submit" class="btn btn-primary">
								<i data-feather="search" style="width: 14px; height: 14px;"></i> Filter
							</button>
							<a href="{{ route('portal.notices') }}" class="btn btn-outline-secondary">
								<i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i> Reset
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Notices List -->
	<div class="row">
		<div class="col-12">
			@if($notices->count() > 0)
				@foreach($notices as $notice)
					<div class="card mb-3 info-card">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-start">
								<div class="flex-grow-1">
									<div class="mb-2">
										<span class="badge {{ $notice->getTypeBadgeClass() }}">
											@if($notice->type === 'urgent')
												<i data-feather="alert-circle" style="width: 12px; height: 12px;"></i>
											@elseif($notice->type === 'announcement')
												<i data-feather="bell" style="width: 12px; height: 12px;"></i>
											@elseif($notice->type === 'circular')
												<i data-feather="file-text" style="width: 12px; height: 12px;"></i>
											@else
												<i data-feather="info" style="width: 12px; height: 12px;"></i>
											@endif
											{{ $notice->getTypeLabel() }}
										</span>
										@if($notice->isExpired())
											<span class="badge badge-light-secondary ms-1">
												<i data-feather="clock" style="width: 12px; height: 12px;"></i> Expired
											</span>
										@endif
									</div>
									<h5 class="mb-2">{{ $notice->title }}</h5>
									<p class="text-muted mb-2">
										<i data-feather="calendar" style="width: 14px; height: 14px;"></i>
										{{ $notice->publish_date->format('M d, Y') }}
										@if($notice->expiry_date)
											<span class="ms-3">
												<i data-feather="clock" style="width: 14px; height: 14px;"></i>
												Expires: {{ $notice->expiry_date->format('M d, Y') }}
											</span>
										@endif
									</p>
									<p class="mb-0 text-muted">{{ Str::limit(strip_tags($notice->content), 200) }}</p>
								</div>
								<div class="ms-3">
									<a href="{{ route('portal.notices.show', $notice) }}" class="btn btn-outline-primary btn-sm">
										<i data-feather="eye" style="width: 14px; height: 14px;"></i> Read More
									</a>
								</div>
							</div>
						</div>
					</div>
				@endforeach

				<div class="mt-3">
					{{ $notices->links() }}
				</div>
			@else
				<div class="card">
					<div class="card-body text-center py-5">
						<i data-feather="bell-off" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
						<h5 class="text-muted">No Notices Available</h5>
						<p class="text-muted mb-0">There are no notices at this time. Check back later for updates.</p>
					</div>
				</div>
			@endif
		</div>
	</div>
</div>
@endsection
