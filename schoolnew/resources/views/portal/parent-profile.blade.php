@extends('layouts.portal')

@section('title', 'My Profile')
@section('page-title', 'Parent Profile')

@section('breadcrumb')
	<li class="breadcrumb-item active">My Profile</li>
@endsection

@section('content')
<div class="container-fluid">
	<!-- Help Tip -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="help-tip">
				<i data-feather="info" class="me-2 text-primary" style="width: 18px; height: 18px;"></i>
				<strong>Your Profile:</strong> View your contact information and linked children. Contact the school office if you need to update any details.
			</div>
		</div>
	</div>

	<div class="row">
		<!-- Profile Card -->
		<div class="col-xl-4 mb-4">
			<div class="card info-card h-100">
				<div class="card-body text-center">
					<div class="position-relative d-inline-block mb-3">
						<img src="{{ $user->avatar_url }}" alt="" class="rounded-circle shadow" width="120" height="120" style="object-fit: cover; border: 4px solid #fff;">
						<span class="position-absolute bottom-0 end-0 badge bg-primary rounded-pill">
							<i data-feather="shield" style="width: 12px; height: 12px;"></i>
						</span>
					</div>
					<h5 class="mb-1">{{ $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? 'Parent' }}</h5>
					<p class="text-muted mb-3">
						<i data-feather="users" style="width: 14px; height: 14px;"></i>
						Parent / Guardian
					</p>

					<hr>

					<div class="text-start">
						<div class="mb-3 d-flex align-items-center">
							<i data-feather="phone" class="text-primary me-2" style="width: 18px; height: 18px;"></i>
							<div>
								<small class="text-muted d-block">Primary Contact</small>
								<span class="fw-medium">{{ $parent->primary_contact ?? 'N/A' }}</span>
							</div>
						</div>
						<div class="d-flex align-items-center">
							<i data-feather="mail" class="text-primary me-2" style="width: 18px; height: 18px;"></i>
							<div>
								<small class="text-muted d-block">Primary Email</small>
								<span class="fw-medium">{{ $parent->primary_email ?? 'N/A' }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Details Tabs -->
		<div class="col-xl-8 mb-4">
			<div class="card">
				<div class="card-header pb-0">
					<ul class="nav nav-tabs border-tab" role="tablist">
						@if($parent->father_name)
							<li class="nav-item">
								<a class="nav-link active" data-bs-toggle="tab" href="#father" role="tab">
									<i data-feather="user" style="width: 14px; height: 14px;"></i> Father
								</a>
							</li>
						@endif
						@if($parent->mother_name)
							<li class="nav-item">
								<a class="nav-link {{ !$parent->father_name ? 'active' : '' }}" data-bs-toggle="tab" href="#mother" role="tab">
									<i data-feather="user" style="width: 14px; height: 14px;"></i> Mother
								</a>
							</li>
						@endif
						@if($parent->guardian_name)
							<li class="nav-item">
								<a class="nav-link {{ !$parent->father_name && !$parent->mother_name ? 'active' : '' }}" data-bs-toggle="tab" href="#guardian" role="tab">
									<i data-feather="shield" style="width: 14px; height: 14px;"></i> Guardian
								</a>
							</li>
						@endif
						<li class="nav-item">
							<a class="nav-link" data-bs-toggle="tab" href="#children" role="tab">
								<i data-feather="users" style="width: 14px; height: 14px;"></i> Children
							</a>
						</li>
					</ul>
				</div>
				<div class="card-body">
					<div class="tab-content">
						@if($parent->father_name)
							<div class="tab-pane fade show active" id="father" role="tabpanel">
								<div class="row">
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="user" style="width: 12px; height: 12px;"></i> Name
											</label>
											<p class="fw-medium mb-0">{{ $parent->father_name }}</p>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="phone" style="width: 12px; height: 12px;"></i> Phone
											</label>
											<p class="fw-medium mb-0">{{ $parent->father_phone ?? 'N/A' }}</p>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="mail" style="width: 12px; height: 12px;"></i> Email
											</label>
											<p class="fw-medium mb-0">{{ $parent->father_email ?? 'N/A' }}</p>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="briefcase" style="width: 12px; height: 12px;"></i> Occupation
											</label>
											<p class="fw-medium mb-0">{{ $parent->father_occupation ?? 'N/A' }}</p>
										</div>
									</div>
								</div>
							</div>
						@endif

						@if($parent->mother_name)
							<div class="tab-pane fade {{ !$parent->father_name ? 'show active' : '' }}" id="mother" role="tabpanel">
								<div class="row">
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="user" style="width: 12px; height: 12px;"></i> Name
											</label>
											<p class="fw-medium mb-0">{{ $parent->mother_name }}</p>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="phone" style="width: 12px; height: 12px;"></i> Phone
											</label>
											<p class="fw-medium mb-0">{{ $parent->mother_phone ?? 'N/A' }}</p>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="mail" style="width: 12px; height: 12px;"></i> Email
											</label>
											<p class="fw-medium mb-0">{{ $parent->mother_email ?? 'N/A' }}</p>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="briefcase" style="width: 12px; height: 12px;"></i> Occupation
											</label>
											<p class="fw-medium mb-0">{{ $parent->mother_occupation ?? 'N/A' }}</p>
										</div>
									</div>
								</div>
							</div>
						@endif

						@if($parent->guardian_name)
							<div class="tab-pane fade {{ !$parent->father_name && !$parent->mother_name ? 'show active' : '' }}" id="guardian" role="tabpanel">
								<div class="row">
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="user" style="width: 12px; height: 12px;"></i> Name
											</label>
											<p class="fw-medium mb-0">{{ $parent->guardian_name }}</p>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="link" style="width: 12px; height: 12px;"></i> Relation
											</label>
											<p class="fw-medium mb-0">{{ $parent->guardian_relation ?? 'N/A' }}</p>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="phone" style="width: 12px; height: 12px;"></i> Phone
											</label>
											<p class="fw-medium mb-0">{{ $parent->guardian_phone ?? 'N/A' }}</p>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="bg-light rounded p-3">
											<label class="text-muted small d-block mb-1">
												<i data-feather="mail" style="width: 12px; height: 12px;"></i> Email
											</label>
											<p class="fw-medium mb-0">{{ $parent->guardian_email ?? 'N/A' }}</p>
										</div>
									</div>
								</div>
							</div>
						@endif

						<div class="tab-pane fade" id="children" role="tabpanel">
							@if($parent->students->count() > 0)
								<div class="row">
									@foreach($parent->students as $student)
										<div class="col-md-6 mb-3">
											<div class="card border info-card h-100">
												<div class="card-body">
													<div class="d-flex align-items-center">
														<img src="{{ $student->photo_url }}" alt="" class="rounded-circle me-3 shadow-sm" width="60" height="60" style="object-fit: cover;">
														<div>
															<h6 class="mb-1">{{ $student->full_name }}</h6>
															<p class="text-muted small mb-1">
																<i data-feather="book-open" style="width: 12px; height: 12px;"></i>
																{{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}
															</p>
															<div class="d-flex flex-wrap gap-1">
																<span class="badge badge-light-primary">
																	<i data-feather="hash" style="width: 10px; height: 10px;"></i>
																	{{ $student->admission_no }}
																</span>
																<span class="badge badge-light-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
																	<i data-feather="{{ $student->status === 'active' ? 'check-circle' : 'pause-circle' }}" style="width: 10px; height: 10px;"></i>
																	{{ ucfirst($student->status) }}
																</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									@endforeach
								</div>
							@else
								<div class="text-center py-5">
									<i data-feather="users" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
									<p class="text-muted mb-0">No children linked to your account.</p>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
