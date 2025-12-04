<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
	<div>
		<!-- Logo -->
		<div class="logo-wrapper">
			<a href="{{ route('portal.dashboard') }}">
				<img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="">
				<img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="">
			</a>
			<div class="back-btn">
				<i class="fa-solid fa-angle-left"></i>
			</div>
			<div class="toggle-sidebar">
				<i class="status_toggle middle sidebar-toggle" data-feather="grid"></i>
			</div>
		</div>
		<div class="logo-icon-wrapper">
			<a href="{{ route('portal.dashboard') }}">
				<img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
			</a>
		</div>

		<!-- Navigation -->
		<nav class="sidebar-main">
			<div class="left-arrow" id="left-arrow">
				<i data-feather="arrow-left"></i>
			</div>
			<div id="sidebar-menu">
				<ul class="sidebar-links" id="simple-bar">
					<li class="back-btn">
						<a href="{{ route('portal.dashboard') }}">
							<img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
						</a>
						<div class="mobile-back text-end">
							<span>Back</span>
							<i class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i>
						</div>
					</li>

					<!-- Main Title -->
					<li class="sidebar-main-title">
						<div><h6>Student Portal</h6></div>
					</li>

					<!-- Dashboard -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}" href="{{ route('portal.dashboard') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
							</svg>
							<span>Dashboard</span>
						</a>
					</li>

					<!-- My Profile -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title" href="{{ route('portal.profile') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
							</svg>
							<span>My Profile</span>
						</a>
					</li>

					<!-- Academic -->
					<li class="sidebar-main-title">
						<div><h6>Academic</h6></div>
					</li>

					<!-- Attendance -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title" href="{{ route('portal.attendance') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-calender') }}"></use>
							</svg>
							<span>My Attendance</span>
						</a>
					</li>

					<!-- Timetable -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title" href="{{ route('portal.timetable') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-board') }}"></use>
							</svg>
							<span>Timetable</span>
						</a>
					</li>

					<!-- Homework -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title" href="{{ route('portal.homework') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-task') }}"></use>
							</svg>
							<span>Homework</span>
						</a>
					</li>

					<!-- Exams & Results -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-file') }}"></use>
							</svg>
							<span>Exams & Results</span>
						</a>
						<ul class="sidebar-submenu">
							<li><a href="{{ route('portal.exams') }}">Exam Schedule</a></li>
							<li><a href="{{ route('portal.results') }}">My Results</a></li>
							<li><a href="{{ route('portal.report-cards') }}">Report Cards</a></li>
						</ul>
					</li>

					<!-- Fees -->
					<li class="sidebar-main-title">
						<div><h6>Finance</h6></div>
					</li>

					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-ecommerce') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-ecommerce') }}"></use>
							</svg>
							<span>Fees</span>
						</a>
						<ul class="sidebar-submenu">
							<li><a href="{{ route('portal.fees.overview') }}">Fee Overview</a></li>
							<li><a href="{{ route('portal.fees.pay') }}">Pay Fees Online</a></li>
							<li><a href="{{ route('portal.fees.history') }}">Payment History</a></li>
							<li><a href="{{ route('portal.fees.receipts') }}">Download Receipts</a></li>
						</ul>
					</li>

					<!-- Communication -->
					<li class="sidebar-main-title">
						<div><h6>Communication</h6></div>
					</li>

					<!-- Notices -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title" href="{{ route('portal.notices') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-email') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-email') }}"></use>
							</svg>
							<span>Notices</span>
						</a>
					</li>

					<!-- Events -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title" href="{{ route('portal.events') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-calender') }}"></use>
							</svg>
							<span>Events</span>
						</a>
					</li>

					<!-- Contact School -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title" href="{{ route('portal.contact') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-contact') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-contact') }}"></use>
							</svg>
							<span>Contact School</span>
						</a>
					</li>
				</ul>
			</div>
			<div class="right-arrow" id="right-arrow">
				<i data-feather="arrow-right"></i>
			</div>
		</nav>
	</div>
</div>
