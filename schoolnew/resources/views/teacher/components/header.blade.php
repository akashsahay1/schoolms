@php
	$user = Auth::user();
	$staff = \App\Models\Staff::where('user_id', $user->id)->first();
@endphp

<div class="page-header">
	<div class="header-wrapper row m-0">
		<!-- Search Form -->
		<form class="form-inline search-full col" action="#" method="get">
			<div class="form-group w-100">
				<div class="Typeahead Typeahead--twitterUsers">
					<div class="u-posRelative">
						<input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search students, classes..." name="q" title="" autofocus>
						<i class="close-search" data-feather="x"></i>
					</div>
				</div>
			</div>
		</form>

		<!-- Logo Wrapper -->
		<div class="header-logo-wrapper col-auto p-0">
			<div class="logo-wrapper">
				<a href="{{ route('teacher.dashboard') }}">
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
						<span class="font-primary">Staff Portal - {{ config('app.name') }}</span>
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

				<!-- Dark/Light Mode Toggle -->
				<li>
					<div class="mode">
						<svg>
							<use href="{{ asset('assets/svg/icon-sprite.svg#moon') }}"></use>
						</svg>
					</div>
				</li>

				<!-- Quick Actions -->
				<li class="onhover-dropdown">
					<div class="notification-box">
						<svg>
							<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
						</svg>
					</div>
					<div class="onhover-show-div notification-dropdown">
						<h6 class="f-18 mb-0 dropdown-title">Quick Actions</h6>
						<ul>
							<li>
								<a href="{{ route('teacher.timetable') }}" class="d-flex align-items-center gap-2">
									<i data-feather="calendar" class="text-primary"></i>
									<span>My Timetable</span>
								</a>
							</li>
							<li>
								<a href="{{ route('teacher.my-classes') }}" class="d-flex align-items-center gap-2">
									<i data-feather="users" class="text-success"></i>
									<span>My Classes</span>
								</a>
							</li>
							<li>
								<a href="{{ route('teacher.profile') }}" class="d-flex align-items-center gap-2">
									<i data-feather="user" class="text-info"></i>
									<span>My Profile</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<!-- Notifications -->
				<li class="onhover-dropdown">
					<div class="notification-box">
						<svg>
							<use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>
						</svg>
						<span class="badge rounded-pill badge-success">0</span>
					</div>
					<div class="onhover-show-div notification-dropdown">
						<h6 class="f-18 mb-0 dropdown-title">Notifications</h6>
						<ul>
							<li class="text-center">
								<p class="text-muted">No new notifications</p>
							</li>
						</ul>
					</div>
				</li>

				<!-- Profile Dropdown -->
				<li class="profile-nav onhover-dropdown pe-0 py-0">
					<div class="d-flex profile-media">
						@if($staff && $staff->photo)
							<img class="b-r-10" src="{{ asset('storage/' . $staff->photo) }}" alt="" style="width: 40px; height: 40px; object-fit: cover;">
						@else
							<img class="b-r-10" src="{{ asset('assets/images/dashboard/profile.png') }}" alt="">
						@endif
						<div class="flex-grow-1">
							<span>{{ $user->name }}</span>
							<p class="mb-0">
								{{ $staff ? ($staff->designation->name ?? 'Staff') : 'Staff' }}
								<i class="middle fa-solid fa-angle-down"></i>
							</p>
						</div>
					</div>
					<ul class="profile-dropdown onhover-show-div">
						<li>
							<a href="{{ route('teacher.profile') }}">
								<i data-feather="user"></i>
								<span>My Profile</span>
							</a>
						</li>
						<li>
							<a href="{{ route('teacher.timetable') }}">
								<i data-feather="calendar"></i>
								<span>My Timetable</span>
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
