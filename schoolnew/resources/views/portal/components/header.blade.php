@php
	$student = \App\Models\Student::where('user_id', Auth::id())->first();
	$parent = \App\Models\ParentGuardian::where('user_id', Auth::id())
		->orWhere('father_email', Auth::user()->email)
		->orWhere('mother_email', Auth::user()->email)
		->orWhere('guardian_email', Auth::user()->email)
		->first();

	$isParent = !$student && $parent;
	$isStudent = (bool) $student;

	if ($isStudent) {
		$photoUrl = $student->photo_url;
		$userName = $student->full_name;
		$userRole = 'Student';
	} elseif ($isParent) {
		$userName = $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? Auth::user()->name;
		$photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=667eea&color=fff&size=40';
		$userRole = 'Parent';
	} else {
		$userName = Auth::user()->name;
		$photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=7366ff&color=fff&size=40';
		$userRole = 'User';
	}
@endphp
<div class="page-header">
	<div class="header-wrapper row m-0">
		<div class="header-logo-wrapper col-auto p-0">
			<div id="sidebar-toggle-btn" class="d-lg-none">
				<svg style="width: 24px; height: 24px; stroke: #2c323f; cursor: pointer;">
					<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-animation') }}"></use>
				</svg>
			</div>
			<div class="logo-wrapper d-lg-none">
				<a href="{{ route('portal.dashboard') }}">
					<img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="">
					<img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="">
				</a>
			</div>
		</div>
		<div class="left-header col horizontal-wrapper ps-0 d-none d-md-flex align-items-center">
			<span class="badge {{ $isStudent ? 'bg-success' : 'bg-primary' }} py-2 px-3">
				<i data-feather="{{ $isStudent ? 'user' : 'users' }}" style="width: 12px; height: 12px;"></i>
				{{ $isStudent ? 'Student Portal' : 'Parent Portal' }}
			</span>
		</div>
		<div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
			<ul class="nav-menus">
				<!-- Quick Actions -->
				<li class="onhover-dropdown">
					<div class="notification-box">
						<svg style="stroke: #2c323f;">
							<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
						</svg>
					</div>
					<div class="onhover-show-div notification-dropdown" style="width: 280px; padding: 15px;">
						<h6 class="mb-3 f-14 dropdown-title" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
							<i data-feather="zap" style="width: 14px; height: 14px;"></i> Quick Actions
						</h6>
						<div class="row g-2">
							<div class="col-6">
								<a href="{{ route('portal.attendance') }}" class="d-block p-2 text-center bg-light rounded text-decoration-none">
									<i data-feather="check-circle" class="text-success d-block mx-auto mb-1" style="width: 20px; height: 20px;"></i>
									<small class="text-dark">Attendance</small>
								</a>
							</div>
							<div class="col-6">
								<a href="{{ route('portal.timetable') }}" class="d-block p-2 text-center bg-light rounded text-decoration-none">
									<i data-feather="clock" class="text-warning d-block mx-auto mb-1" style="width: 20px; height: 20px;"></i>
									<small class="text-dark">Timetable</small>
								</a>
							</div>
							<div class="col-6">
								<a href="{{ route('portal.fees.overview') }}" class="d-block p-2 text-center bg-light rounded text-decoration-none">
									<i data-feather="credit-card" class="text-primary d-block mx-auto mb-1" style="width: 20px; height: 20px;"></i>
									<small class="text-dark">Fees</small>
								</a>
							</div>
							<div class="col-6">
								<a href="{{ route('portal.notices') }}" class="d-block p-2 text-center bg-light rounded text-decoration-none">
									<i data-feather="bell" class="text-info d-block mx-auto mb-1" style="width: 20px; height: 20px;"></i>
									<small class="text-dark">Notices</small>
								</a>
							</div>
						</div>
					</div>
				</li>

				<!-- Notifications -->
				<li class="onhover-dropdown" id="notification-dropdown">
					<div class="notification-box">
						<svg style="stroke: #2c323f;">
							<use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>
						</svg>
						<span class="badge rounded-pill badge-success notification-badge" style="{{ ($unreadNotificationCount ?? 0) == 0 ? 'display:none;' : '' }}">{{ $unreadNotificationCount ?? 0 }}</span>
					</div>
					<div class="onhover-show-div notification-dropdown">
						<h6 class="f-18 mb-0 dropdown-title d-flex justify-content-between align-items-center">
							Notifications
							<a href="javascript:void(0)" class="mark-all-read text-primary f-12" style="{{ ($unreadNotificationCount ?? 0) == 0 ? 'display:none;' : '' }}">Mark all read</a>
						</h6>
						<ul id="notification-list">
							@if(($unreadNotificationCount ?? 0) == 0)
								<li class="text-center no-notifications">
									<p class="text-muted">No new notifications</p>
								</li>
							@endif
						</ul>
					</div>
				</li>

				<!-- User Profile -->
				<li class="profile-nav onhover-dropdown pe-0 py-0 me-0">
					<div class="d-flex align-items-center profile-media">
						<img class="b-r-10" src="{{ $photoUrl }}" alt="{{ $userName }}" width="40" height="40" style="border-radius: 10px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=7366ff&color=fff&size=40'">
						<div class="flex-grow-1 user">
							<span style="color: #2c323f !important;">{{ Str::limit($userName, 15) }}</span>
							<p class="mb-0" style="color: #6c757d !important;">{{ $userRole }}<i class="middle fa fa-angle-down ms-1" style="color: #2c323f;"></i></p>
						</div>
					</div>
					<ul class="profile-dropdown onhover-show-div">
						<li>
							<a href="{{ route('portal.profile') }}">
								<i data-feather="user"></i><span>My Profile</span>
							</a>
						</li>
						<li>
							<a href="{{ route('portal.attendance') }}">
								<i data-feather="check-circle"></i><span>Attendance</span>
							</a>
						</li>
						<li>
							<a href="{{ route('portal.fees.overview') }}">
								<i data-feather="credit-card"></i><span>Fee Overview</span>
							</a>
						</li>
						<li>
							<a href="{{ route('portal.contact') }}">
								<i data-feather="message-circle"></i><span>Contact School</span>
							</a>
						</li>
						<li style="border-top: 1px solid #eee; margin-top: 5px; padding-top: 10px;">
							<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('portal-logout-form').submit();">
								<i data-feather="log-out" class="text-danger"></i><span class="text-danger">Log out</span>
							</a>
						</li>
					</ul>
					<form method="POST" action="{{ route('logout') }}" id="portal-logout-form" style="display: none;">@csrf</form>
				</li>
			</ul>
		</div>
	</div>
</div>
