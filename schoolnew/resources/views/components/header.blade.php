<div class="page-header">
	<div class="header-wrapper row m-0">
		<!-- Search Form -->
		<form class="form-inline search-full col" action="#" method="get">
			<div class="form-group w-100">
				<div class="Typeahead Typeahead--twitterUsers">
					<div class="u-posRelative">
						<input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search Anything Here..." name="q" title="" autofocus>
						<div class="spinner-border Typeahead-spinner" role="status">
							<span class="sr-only">Loading...</span>
						</div>
						<i class="close-search" data-feather="x"></i>
					</div>
					<div class="Typeahead-menu"></div>
				</div>
			</div>
		</form>

		<!-- Logo Wrapper -->
		<div class="header-logo-wrapper col-auto p-0">
			<div id="sidebar-toggle-btn" class="d-lg-none">
				<svg style="width: 24px; height: 24px; stroke: #2c323f; cursor: pointer;">
					<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-animation') }}"></use>
				</svg>
			</div>
			@php $__logo = \App\Models\Setting::get('school_logo'); @endphp
			<div class="logo-wrapper d-lg-none">
				<a href="{{ route('admin.dashboard') }}">
					@if($__logo)
						<img class="img-fluid for-light" src="{{ asset('storage/' . $__logo) }}" alt="">
						<img class="img-fluid for-dark" src="{{ asset('storage/' . $__logo) }}" alt="">
					@else
						<span style="font-size: 16px; font-weight: 600; color: #2c323f;">{{ \App\Models\Setting::get('school_name', config('app.name')) }}</span>
					@endif
				</a>
			</div>
		</div>

		<!-- Left Header -->
		<div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
			<div class="notification-slider">
				<div class="d-flex h-100">
					<h6 class="mb-0 f-w-400" style="color: #fff !important;">
						<span style="color: #fff !important;">Welcome to {{ config('app.name') }}! </span>
					</h6>
				</div>
			</div>
		</div>

		<!-- Right Header -->
		<div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
			<ul class="nav-menus">
				<!-- Fullscreen Toggle -->
				<li class="fullscreen-body">
					<span>
						<svg id="maximize-screen" style="stroke: #2c323f;">
							<use href="{{ asset('assets/svg/icon-sprite.svg#full-screen') }}"></use>
						</svg>
					</span>
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

				<!-- Profile Dropdown -->
				<li class="profile-nav onhover-dropdown pe-0 py-0">
					<div class="d-flex profile-media">
						<img class="b-r-10" src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" width="40" height="40" style="border-radius: 10px;">
						<div class="flex-grow-1">
							<span style="color: #2c323f;">{{ Auth::user()->name }}</span>
							<p class="mb-0" style="color: #6c757d;">
								{{ Auth::user()->roles->first()->name ?? 'Administrator' }}
								<i class="middle fa-solid fa-angle-down" style="color: #2c323f;"></i>
							</p>
						</div>
					</div>
					<ul class="profile-dropdown onhover-show-div">
						<li><a href="{{ route('admin.profile') }}"><i data-feather="user"></i><span>Account</span></a></li>
						<li><a href="{{ route('admin.settings.index') }}"><i data-feather="settings"></i><span>Settings</span></a></li>
						<li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();"><i data-feather="log-out"></i><span>Log out</span></a></li>
					</ul>
					<form method="POST" action="{{ route('logout') }}" id="admin-logout-form" style="display: none;">@csrf</form>
				</li>
			</ul>
		</div>
	</div>
</div>
