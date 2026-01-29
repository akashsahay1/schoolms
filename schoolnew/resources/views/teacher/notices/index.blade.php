@extends('layouts.teacher-portal')

@section('title', 'Notices')
@section('page-title', 'Notices')

@section('breadcrumb')
<li class="breadcrumb-item active">Notices</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header pb-0">
				<h5 class="mb-0">School Notices</h5>
			</div>
			<div class="card-body">
				@if($notices->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($notices as $notice)
							<a href="{{ route('teacher.notices.show', $notice) }}" class="list-group-item list-group-item-action">
								<div class="d-flex justify-content-between align-items-start">
									<div>
										<h6 class="mb-1">{{ $notice->title }}</h6>
										<p class="text-muted mb-1 small">{{ Str::limit(strip_tags($notice->content), 150) }}</p>
										<small class="text-muted">
											<i data-feather="calendar" style="width: 14px; height: 14px;"></i>
											{{ $notice->publish_date->format('M d, Y') }}
										</small>
									</div>
									<span class="badge bg-{{ $notice->priority == 'high' ? 'danger' : ($notice->priority == 'medium' ? 'warning' : 'info') }}">
										{{ ucfirst($notice->priority ?? 'normal') }}
									</span>
								</div>
							</a>
						@endforeach
					</div>
					<div class="mt-4">
						{{ $notices->links() }}
					</div>
				@else
					<div class="text-center py-5">
						<i data-feather="bell-off" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
						<h5 class="text-muted">No Notices</h5>
						<p class="text-muted mb-0">There are no notices to display at this time.</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
