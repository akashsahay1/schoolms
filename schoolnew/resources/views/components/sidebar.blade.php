<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
	<div>
		<!-- Logo -->
		<div class="logo-wrapper">
			<a href="{{ route('admin.dashboard') }}">
				<img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="">
				<img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="">
			</a>
			<div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
			<div class="toggle-sidebar">
				<i class="status_toggle middle sidebar-toggle" data-feather="grid"></i>
			</div>
		</div>
		<div class="logo-icon-wrapper">
			<a href="{{ route('admin.dashboard') }}">
				<img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
			</a>
		</div>

		<!-- Navigation -->
		<nav class="sidebar-main">
			<div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
			<div id="sidebar-menu">
				<ul class="sidebar-links" id="simple-bar">
					<li class="back-btn">
						<a href="{{ route('admin.dashboard') }}">
							<img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
						</a>
						<div class="mobile-back text-end">
							<span>Back</span>
							<i class="fa-solid fa-angle-right ps-2"></i>
						</div>
					</li>

					<!-- Main -->
					<li class="sidebar-main-title">
						<div><h6>Main</h6></div>
					</li>

					<!-- Dashboard -->
					<li class="sidebar-list">
						<i class="fa-solid fa-thumbtack"></i>
						<a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
							</svg>
							<span>Dashboard</span>
						</a>
					</li>

					<!-- Academic -->
					<li class="sidebar-main-title">
						<div><h6>Academic</h6></div>
					</li>

					<!-- Students -->
					<li class="sidebar-list {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
						<i class="fa-solid fa-thumbtack"></i>
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
							</svg>
							<span>Students</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.students.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.students.index') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">All Students</a></li>
							<li><a class="{{ request()->routeIs('admin.students.create') ? 'active' : '' }}" href="{{ route('admin.students.create') }}">Add Student</a></li>
						</ul>
					</li>

					<!-- Academics (Classes, Sections, Subjects, Teachers, Parents) -->
					<li class="sidebar-list {{ request()->routeIs('admin.classes.*') || request()->routeIs('admin.sections.*') || request()->routeIs('admin.subjects.*') || request()->routeIs('admin.teachers.*') || request()->routeIs('admin.parents.*') ? 'active' : '' }}">
						<i class="fa-solid fa-thumbtack"></i>
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.classes.*') || request()->routeIs('admin.sections.*') || request()->routeIs('admin.subjects.*') || request()->routeIs('admin.teachers.*') || request()->routeIs('admin.parents.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-board') }}"></use>
							</svg>
							<span>Academics</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.classes.*') || request()->routeIs('admin.sections.*') || request()->routeIs('admin.subjects.*') || request()->routeIs('admin.teachers.*') || request()->routeIs('admin.parents.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}" href="{{ route('admin.classes.index') }}">Classes</a></li>
							<li><a class="{{ request()->routeIs('admin.sections.*') ? 'active' : '' }}" href="{{ route('admin.sections.index') }}">Sections</a></li>
							<li><a class="{{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}" href="{{ route('admin.subjects.index') }}">Subjects</a></li>
							<li><a class="{{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}" href="{{ route('admin.teachers.index') }}">Teachers</a></li>
							<li><a class="{{ request()->routeIs('admin.parents.*') ? 'active' : '' }}" href="{{ route('admin.parents.index') }}">Parents</a></li>
						</ul>
					</li>

					<!-- Attendance -->
					<li class="sidebar-list">
						<i class="fa-solid fa-thumbtack"></i>
						<a class="sidebar-link sidebar-title" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-calender') }}"></use>
							</svg>
							<span>Attendance</span>
						</a>
						<ul class="sidebar-submenu">
							<li><a href="{{ route('admin.attendance.mark') }}">Mark Attendance</a></li>
							<li><a href="{{ route('admin.attendance.reports') }}">Reports</a></li>
						</ul>
					</li>

					<!-- Examinations -->
					<li class="sidebar-list">
						<i class="fa-solid fa-thumbtack"></i>
						<a class="sidebar-link sidebar-title" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-file') }}"></use>
							</svg>
							<span>Examinations</span>
						</a>
						<ul class="sidebar-submenu">
							<li><a href="{{ route('admin.exams.index') }}">Exam Schedule</a></li>
							<li><a href="{{ route('admin.exams.marks') }}">Marks Entry</a></li>
							<li><a href="{{ route('admin.exams.results') }}">Results</a></li>
						</ul>
					</li>

					<!-- Finance -->
					<li class="sidebar-main-title">
						<div><h6>Finance</h6></div>
					</li>

					<!-- Fees -->
					<li class="sidebar-list">
						<i class="fa-solid fa-thumbtack"></i>
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
							<li><a href="{{ route('admin.fees.structure') }}">Fee Structure</a></li>
							<li><a href="{{ route('admin.fees.collection') }}">Collect Fees</a></li>
							<li><a href="{{ route('admin.fees.outstanding') }}">Outstanding</a></li>
						</ul>
					</li>

					<!-- Administration -->
					<li class="sidebar-main-title">
						<div><h6>Administration</h6></div>
					</li>

					<!-- Users (Accountant, Administrator - not Students, Parents, Teachers) -->
					<li class="sidebar-list {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
						<i class="fa-solid fa-thumbtack"></i>
						<a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
							</svg>
							<span>Users</span>
						</a>
					</li>

					<!-- Settings -->
					<li class="sidebar-list">
						<i class="fa-solid fa-thumbtack"></i>
						<a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#setting') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#setting') }}"></use>
							</svg>
							<span>Settings</span>
						</a>
					</li>

				</ul>
			</div>
			<div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
		</nav>
	</div>
</div>
