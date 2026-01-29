@php
	$user = Auth::user();
	$staff = \App\Models\Staff::where('user_id', $user->id)->first();
@endphp

<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
	<div>
		<!-- Logo -->
		<div class="logo-wrapper">
			<a href="{{ route('teacher.dashboard') }}">
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
			<a href="{{ route('teacher.dashboard') }}">
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
						<a href="{{ route('teacher.dashboard') }}">
							<img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
						</a>
						<div class="mobile-back text-end">
							<span>Back</span>
							<i class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i>
						</div>
					</li>

					<!-- Main Title -->
					<li class="sidebar-main-title">
						<div><h6>Staff Portal</h6></div>
					</li>

					<!-- Dashboard -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">
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
						<a class="sidebar-link sidebar-title {{ request()->routeIs('teacher.profile') ? 'active' : '' }}" href="{{ route('teacher.profile') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
							</svg>
							<span>My Profile</span>
						</a>
					</li>

					<!-- Teaching Section -->
					<li class="sidebar-main-title">
						<div><h6>Teaching</h6></div>
					</li>

					<!-- My Timetable -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('teacher.timetable') ? 'active' : '' }}" href="{{ route('teacher.timetable') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-calender') }}"></use>
							</svg>
							<span>My Timetable</span>
						</a>
					</li>

					<!-- My Classes -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('teacher.my-classes') || request()->routeIs('teacher.class-students') ? 'active' : '' }}" href="{{ route('teacher.my-classes') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-learning') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-learning') }}"></use>
							</svg>
							<span>My Classes</span>
						</a>
					</li>

					<!-- Homework (if teacher) -->
					@if($staff && $staff->designation && str_contains(strtolower($staff->designation->name), 'teacher'))
					<li class="sidebar-list {{ request()->routeIs('teacher.homework.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('teacher.homework.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-task') }}"></use>
							</svg>
							<span>Homework</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('teacher.homework.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('teacher.homework.index') ? 'active' : '' }}" href="{{ route('teacher.homework.index') }}">All Homework</a></li>
							<li><a class="{{ request()->routeIs('teacher.homework.create') ? 'active' : '' }}" href="{{ route('teacher.homework.create') }}">Assign Homework</a></li>
							<li><a class="{{ request()->routeIs('teacher.homework.submissions') ? 'active' : '' }}" href="{{ route('teacher.homework.submissions') }}">Review Submissions</a></li>
						</ul>
					</li>

					<!-- Attendance -->
					<li class="sidebar-list {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-board') }}"></use>
							</svg>
							<span>Attendance</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('teacher.attendance.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('teacher.attendance.mark') ? 'active' : '' }}" href="{{ route('teacher.attendance.mark') }}">Mark Attendance</a></li>
							<li><a class="{{ request()->routeIs('teacher.attendance.reports') ? 'active' : '' }}" href="{{ route('teacher.attendance.reports') }}">View Reports</a></li>
						</ul>
					</li>

					<!-- Exams -->
					<li class="sidebar-list {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-file') }}"></use>
							</svg>
							<span>Exams</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('teacher.exams.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('teacher.exams.schedule') ? 'active' : '' }}" href="{{ route('teacher.exams.schedule') }}">Exam Schedule</a></li>
							<li><a class="{{ request()->routeIs('teacher.exams.marks') ? 'active' : '' }}" href="{{ route('teacher.exams.marks') }}">Enter Marks</a></li>
						</ul>
					</li>
					@endif

					<!-- Leave Management -->
					<li class="sidebar-main-title">
						<div><h6>Leave</h6></div>
					</li>

					<li class="sidebar-list {{ request()->routeIs('teacher.leaves.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('teacher.leaves.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-editors') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-editors') }}"></use>
							</svg>
							<span>My Leave</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('teacher.leaves.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('teacher.leaves.index') ? 'active' : '' }}" href="{{ route('teacher.leaves.index') }}">My Applications</a></li>
							<li><a class="{{ request()->routeIs('teacher.leaves.create') ? 'active' : '' }}" href="{{ route('teacher.leaves.create') }}">Apply for Leave</a></li>
							<li><a class="{{ request()->routeIs('teacher.leaves.balance') ? 'active' : '' }}" href="{{ route('teacher.leaves.balance') }}">Leave Balance</a></li>
						</ul>
					</li>

					<!-- Communication -->
					<li class="sidebar-main-title">
						<div><h6>Communication</h6></div>
					</li>

					<!-- Notices -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('teacher.notices*') ? 'active' : '' }}" href="{{ route('teacher.notices') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-form') }}"></use>
							</svg>
							<span>Notices</span>
						</a>
					</li>

					<!-- Events -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('teacher.events*') ? 'active' : '' }}" href="{{ route('teacher.events') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-calender') }}"></use>
							</svg>
							<span>Events</span>
						</a>
					</li>

					<!-- Messages -->
					<li class="sidebar-list {{ request()->routeIs('teacher.messages.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('teacher.messages.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-email') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-email') }}"></use>
							</svg>
							<span>Messages</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('teacher.messages.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('teacher.messages.index') ? 'active' : '' }}" href="{{ route('teacher.messages.index') }}">Inbox</a></li>
							<li><a class="{{ request()->routeIs('teacher.messages.compose') ? 'active' : '' }}" href="{{ route('teacher.messages.compose') }}">Compose</a></li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="right-arrow" id="right-arrow">
				<i data-feather="arrow-right"></i>
			</div>
		</nav>
	</div>
</div>
