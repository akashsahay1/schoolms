@extends('layouts.portal')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

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
				<strong>Your Profile:</strong> View your personal, academic, and parent/guardian information. Contact the school office if you need to update any details.
			</div>
		</div>
	</div>

	<div class="row">
		<!-- Profile Card -->
		<div class="col-xl-4">
			<div class="card info-card">
				<div class="card-body text-center">
					<div class="position-relative d-inline-block mb-3">
						<img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="rounded-circle shadow" width="120" height="120" style="object-fit: cover; border: 4px solid #fff;">
						<span class="position-absolute bottom-0 end-0 badge badge-light-{{ $student->status === 'active' ? 'success' : 'secondary' }} rounded-pill">
							<i data-feather="{{ $student->status === 'active' ? 'check-circle' : 'pause-circle' }}" style="width: 12px; height: 12px;"></i>
							{{ ucfirst($student->status ?? 'Active') }}
						</span>
					</div>
					<h5 class="mb-1">{{ $student->full_name }}</h5>
					<p class="text-muted mb-3">
						<i data-feather="book-open" style="width: 14px; height: 14px;"></i>
						{{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}
					</p>
					<div class="mb-3">
						<span class="badge badge-light-primary mb-1">
							<i data-feather="hash" style="width: 12px; height: 12px;"></i> Adm: {{ $student->admission_no }}
						</span>
						@if($student->roll_no)
							<span class="badge badge-light-secondary mb-1">
								<i data-feather="user" style="width: 12px; height: 12px;"></i> Roll: {{ $student->roll_no }}
							</span>
						@endif
					</div>
					<hr>
					<div class="row text-start">
						<div class="col-6 mb-3">
							<div class="d-flex align-items-center">
								<i data-feather="mail" class="text-primary me-2" style="width: 16px; height: 16px;"></i>
								<div>
									<small class="text-muted d-block">Email</small>
									<span class="fw-medium">{{ $student->email ?? $user->email ?? 'N/A' }}</span>
								</div>
							</div>
						</div>
						<div class="col-6 mb-3">
							<div class="d-flex align-items-center">
								<i data-feather="phone" class="text-primary me-2" style="width: 16px; height: 16px;"></i>
								<div>
									<small class="text-muted d-block">Phone</small>
									<span class="fw-medium">{{ $student->phone ?? 'N/A' }}</span>
								</div>
							</div>
						</div>
						<div class="col-6 mb-2">
							<div class="d-flex align-items-center">
								<i data-feather="calendar" class="text-primary me-2" style="width: 16px; height: 16px;"></i>
								<div>
									<small class="text-muted d-block">Date of Birth</small>
									<span class="fw-medium">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</span>
								</div>
							</div>
						</div>
						<div class="col-6 mb-2">
							<div class="d-flex align-items-center">
								<i data-feather="user" class="text-primary me-2" style="width: 16px; height: 16px;"></i>
								<div>
									<small class="text-muted d-block">Gender</small>
									<span class="fw-medium">{{ ucfirst($student->gender ?? 'N/A') }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Details Tabs -->
		<div class="col-xl-8">
			<div class="card">
				<div class="card-header pb-0">
					<ul class="nav nav-tabs border-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-bs-toggle="tab" href="#personal" role="tab">
								<i data-feather="user" style="width: 14px; height: 14px;"></i> Personal Info
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-bs-toggle="tab" href="#academic" role="tab">
								<i data-feather="book" style="width: 14px; height: 14px;"></i> Academic Info
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-bs-toggle="tab" href="#parent" role="tab">
								<i data-feather="users" style="width: 14px; height: 14px;"></i> Parent/Guardian
							</a>
						</li>
					</ul>
				</div>
				<div class="card-body">
					<div class="tab-content">
						<!-- Personal Info Tab -->
						<div class="tab-pane fade show active" id="personal" role="tabpanel">
							<div class="row">
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="user" style="width: 12px; height: 12px;"></i> First Name
										</label>
										<p class="mb-0 fw-medium">{{ $student->first_name }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="user" style="width: 12px; height: 12px;"></i> Last Name
										</label>
										<p class="mb-0 fw-medium">{{ $student->last_name }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="droplet" style="width: 12px; height: 12px;"></i> Blood Group
										</label>
										<p class="mb-0 fw-medium">{{ $student->blood_group ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="heart" style="width: 12px; height: 12px;"></i> Religion
										</label>
										<p class="mb-0 fw-medium">{{ $student->religion ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="flag" style="width: 12px; height: 12px;"></i> Nationality
										</label>
										<p class="mb-0 fw-medium">{{ $student->nationality ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="message-circle" style="width: 12px; height: 12px;"></i> Mother Tongue
										</label>
										<p class="mb-0 fw-medium">{{ $student->mother_tongue ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="map-pin" style="width: 12px; height: 12px;"></i> Current Address
										</label>
										<p class="mb-0 fw-medium">{{ $student->current_address ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="home" style="width: 12px; height: 12px;"></i> Permanent Address
										</label>
										<p class="mb-0 fw-medium">{{ $student->permanent_address ?? 'N/A' }}</p>
									</div>
								</div>
							</div>
						</div>

						<!-- Academic Info Tab -->
						<div class="tab-pane fade" id="academic" role="tabpanel">
							<div class="row">
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="hash" style="width: 12px; height: 12px;"></i> Admission Number
										</label>
										<p class="mb-0 fw-medium text-primary">{{ $student->admission_no }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="list" style="width: 12px; height: 12px;"></i> Roll Number
										</label>
										<p class="mb-0 fw-medium">{{ $student->roll_no ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="book-open" style="width: 12px; height: 12px;"></i> Class
										</label>
										<p class="mb-0 fw-medium">{{ $student->schoolClass->name ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="grid" style="width: 12px; height: 12px;"></i> Section
										</label>
										<p class="mb-0 fw-medium">{{ $student->section->name ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="calendar" style="width: 12px; height: 12px;"></i> Academic Year
										</label>
										<p class="mb-0 fw-medium">{{ $student->academicYear->name ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="log-in" style="width: 12px; height: 12px;"></i> Admission Date
										</label>
										<p class="mb-0 fw-medium">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'N/A' }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="briefcase" style="width: 12px; height: 12px;"></i> Previous School
										</label>
										<p class="mb-0 fw-medium">{{ $student->previous_school ?? 'N/A' }}</p>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="bg-light rounded p-3">
										<label class="text-muted small d-block mb-1">
											<i data-feather="activity" style="width: 12px; height: 12px;"></i> Status
										</label>
										<p class="mb-0">
											<span class="badge badge-light-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
												<i data-feather="{{ $student->status === 'active' ? 'check-circle' : 'pause-circle' }}" style="width: 12px; height: 12px;"></i>
												{{ ucfirst($student->status) }}
											</span>
										</p>
									</div>
								</div>
							</div>
						</div>

						<!-- Parent/Guardian Tab -->
						<div class="tab-pane fade" id="parent" role="tabpanel">
							@if($student->parent)
								<div class="row">
									@if($student->parent->father_name)
										<div class="col-md-6 mb-4">
											<div class="card border h-100">
												<div class="card-header bg-light-primary py-2">
													<h6 class="mb-0 text-primary">
														<i data-feather="user" style="width: 16px; height: 16px;"></i> Father's Information
													</h6>
												</div>
												<div class="card-body">
													<div class="mb-3">
														<small class="text-muted d-block">
															<i data-feather="user" style="width: 12px; height: 12px;"></i> Name
														</small>
														<span class="fw-medium">{{ $student->parent->father_name }}</span>
													</div>
													<div class="mb-3">
														<small class="text-muted d-block">
															<i data-feather="phone" style="width: 12px; height: 12px;"></i> Phone
														</small>
														<span class="fw-medium">{{ $student->parent->father_phone ?? 'N/A' }}</span>
													</div>
													<div class="mb-3">
														<small class="text-muted d-block">
															<i data-feather="mail" style="width: 12px; height: 12px;"></i> Email
														</small>
														<span class="fw-medium">{{ $student->parent->father_email ?? 'N/A' }}</span>
													</div>
													<div>
														<small class="text-muted d-block">
															<i data-feather="briefcase" style="width: 12px; height: 12px;"></i> Occupation
														</small>
														<span class="fw-medium">{{ $student->parent->father_occupation ?? 'N/A' }}</span>
													</div>
												</div>
											</div>
										</div>
									@endif

									@if($student->parent->mother_name)
										<div class="col-md-6 mb-4">
											<div class="card border h-100">
												<div class="card-header bg-light-info py-2">
													<h6 class="mb-0 text-info">
														<i data-feather="user" style="width: 16px; height: 16px;"></i> Mother's Information
													</h6>
												</div>
												<div class="card-body">
													<div class="mb-3">
														<small class="text-muted d-block">
															<i data-feather="user" style="width: 12px; height: 12px;"></i> Name
														</small>
														<span class="fw-medium">{{ $student->parent->mother_name }}</span>
													</div>
													<div class="mb-3">
														<small class="text-muted d-block">
															<i data-feather="phone" style="width: 12px; height: 12px;"></i> Phone
														</small>
														<span class="fw-medium">{{ $student->parent->mother_phone ?? 'N/A' }}</span>
													</div>
													<div class="mb-3">
														<small class="text-muted d-block">
															<i data-feather="mail" style="width: 12px; height: 12px;"></i> Email
														</small>
														<span class="fw-medium">{{ $student->parent->mother_email ?? 'N/A' }}</span>
													</div>
													<div>
														<small class="text-muted d-block">
															<i data-feather="briefcase" style="width: 12px; height: 12px;"></i> Occupation
														</small>
														<span class="fw-medium">{{ $student->parent->mother_occupation ?? 'N/A' }}</span>
													</div>
												</div>
											</div>
										</div>
									@endif

									@if($student->parent->guardian_name)
										<div class="col-md-6 mb-4">
											<div class="card border h-100">
												<div class="card-header bg-light-warning py-2">
													<h6 class="mb-0 text-warning">
														<i data-feather="shield" style="width: 16px; height: 16px;"></i> Guardian's Information
													</h6>
												</div>
												<div class="card-body">
													<div class="mb-3">
														<small class="text-muted d-block">
															<i data-feather="user" style="width: 12px; height: 12px;"></i> Name
														</small>
														<span class="fw-medium">{{ $student->parent->guardian_name }}</span>
													</div>
													<div class="mb-3">
														<small class="text-muted d-block">
															<i data-feather="link" style="width: 12px; height: 12px;"></i> Relation
														</small>
														<span class="fw-medium">{{ $student->parent->guardian_relation ?? 'N/A' }}</span>
													</div>
													<div class="mb-3">
														<small class="text-muted d-block">
															<i data-feather="phone" style="width: 12px; height: 12px;"></i> Phone
														</small>
														<span class="fw-medium">{{ $student->parent->guardian_phone ?? 'N/A' }}</span>
													</div>
													<div>
														<small class="text-muted d-block">
															<i data-feather="mail" style="width: 12px; height: 12px;"></i> Email
														</small>
														<span class="fw-medium">{{ $student->parent->guardian_email ?? 'N/A' }}</span>
													</div>
												</div>
											</div>
										</div>
									@endif
								</div>
							@else
								<div class="text-center py-5">
									<i data-feather="users" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
									<p class="text-muted mb-0">No parent/guardian information available</p>
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
