@php
	$headerUser = Auth::user();
	$isStudentUser = \App\Models\Student::where('user_id', $headerUser->id)->exists();

	// Check if parent by user_id OR by email match
	$isParentHeader = false;
	if (!$isStudentUser) {
		$isParentHeader = \App\Models\ParentGuardian::where('user_id', $headerUser->id)->exists()
			|| \App\Models\ParentGuardian::where('father_email', $headerUser->email)->exists()
			|| \App\Models\ParentGuardian::where('mother_email', $headerUser->email)->exists()
			|| \App\Models\ParentGuardian::where('guardian_email', $headerUser->email)->exists();
	}
@endphp

<div class="page-header">
	<div class="header-wrapper row m-0">
		<!-- Search Form -->
		<form class="form-inline search-full col" action="#" method="get">
			<div class="form-group w-100">
				<div class="Typeahead Typeahead--twitterUsers">
					<div class="u-posRelative">
						<input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search..." name="q" title="" autofocus>
						<i class="close-search" data-feather="x"></i>
					</div>
				</div>
			</div>
		</form>

		<!-- Logo Wrapper -->
		<div class="header-logo-wrapper col-auto p-0">
			<div class="logo-wrapper">
				<a href="{{ route('portal.dashboard') }}">
					<img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="">
					<img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="">
				</a>
			</div>
			<div class="toggle-sidebar">
				<i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
			</div>
		</div>

		<!-- Left Header -->
		<div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
			<div class="notification-slider">
				<div class="d-flex h-100">
					<h6 class="mb-0 f-w-400">
						<span class="font-primary">{{ $isParentHeader ? 'Parent' : 'Student' }} Portal - {{ config('app.name') }}</span>
					</h6>
				</div>
			</div>
		</div>

		<!-- Right Header -->
		<div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
			<ul class="nav-menus">
				<!-- Search Toggle -->
				<li>
					<span class="header-search">
						<svg>
							<use href="{{ asset('assets/svg/icon-sprite.svg#search') }}"></use>
						</svg>
					</span>
				</li>

				<!-- Notifications -->
				<li class="onhover-dropdown" id="notification-dropdown">
					<div class="notification-box">
						<svg>
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

				<!-- Profile Dropdown -->
				<li class="profile-nav onhover-dropdown pe-0 py-0">
					<div class="d-flex profile-media">
						<img class="b-r-10" src="{{ asset('assets/images/dashboard/profile.png') }}" alt="">
						<div class="flex-grow-1">
							<span>{{ Auth::check() ? Auth::user()->name : 'User' }}</span>
							<p class="mb-0">
								{{ $isParentHeader ? 'Parent' : 'Student' }}
								<i class="middle fa-solid fa-angle-down"></i>
							</p>
						</div>
					</div>
					<ul class="profile-dropdown onhover-show-div">
						<li>
							<a href="{{ route('portal.profile') }}">
								<i data-feather="user"></i>
								<span>{{ $isParentHeader ? 'Child Profile' : 'My Profile' }}</span>
							</a>
						</li>
						<li>
							<form method="POST" action="{{ route('logout') }}">
								@csrf
								<a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
									<i data-feather="log-out"></i>
									<span>Log out</span>
								</a>
							</form>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>
