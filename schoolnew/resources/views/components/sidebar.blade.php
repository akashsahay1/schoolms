<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
	<div>
		<!-- Logo -->
		<div class="logo-wrapper">
			<a href="{{ route('admin.dashboard') }}">
				<img class="img-fluid for-light logo-custom" src="{{ asset('assets/images/logo/logo.png') }}" alt="">
				<img class="img-fluid for-dark logo-custom" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="">
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
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
							</svg>
							<span>Students</span>
						</a>
					</li>

					<!-- Academics (Classes, Sections, Subjects, Teachers, Parents) -->
					<li class="sidebar-list {{ request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.classes.*') || request()->routeIs('admin.sections.*') || request()->routeIs('admin.subjects.*') || request()->routeIs('admin.teachers.*') || request()->routeIs('admin.parents.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.classes.*') || request()->routeIs('admin.sections.*') || request()->routeIs('admin.subjects.*') || request()->routeIs('admin.teachers.*') || request()->routeIs('admin.parents.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-board') }}"></use>
							</svg>
							<span>Academics</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.classes.*') || request()->routeIs('admin.sections.*') || request()->routeIs('admin.subjects.*') || request()->routeIs('admin.teachers.*') || request()->routeIs('admin.parents.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}" href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
							<li><a class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}" href="{{ route('admin.classes.index') }}">Classes</a></li>
							<li><a class="{{ request()->routeIs('admin.sections.*') ? 'active' : '' }}" href="{{ route('admin.sections.index') }}">Sections</a></li>
							<li><a class="{{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}" href="{{ route('admin.subjects.index') }}">Subjects</a></li>
							<li><a class="{{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}" href="{{ route('admin.teachers.index') }}">Teachers</a></li>
							<li><a class="{{ request()->routeIs('admin.parents.*') ? 'active' : '' }}" href="{{ route('admin.parents.index') }}">Parents</a></li>
						</ul>
					</li>

					<!-- Timetable -->
					<li class="sidebar-list {{ request()->routeIs('admin.timetable.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.timetable.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-calender') }}"></use>
							</svg>
							<span>Timetable</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.timetable.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.timetable.index') ? 'active' : '' }}" href="{{ route('admin.timetable.index') }}">View Timetable</a></li>
							<li><a class="{{ request()->routeIs('admin.timetable.create') ? 'active' : '' }}" href="{{ route('admin.timetable.create') }}">Create Timetable</a></li>
							<li><a class="{{ request()->routeIs('admin.timetable.teacher') ? 'active' : '' }}" href="{{ route('admin.timetable.teacher') }}">Teacher Timetable</a></li>
							<li><a class="{{ request()->routeIs('admin.timetable.periods') ? 'active' : '' }}" href="{{ route('admin.timetable.periods') }}">Manage Periods</a></li>
							<li><a class="{{ request()->routeIs('admin.timetable.conflicts') ? 'active' : '' }}" href="{{ route('admin.timetable.conflicts') }}">Conflict Check</a></li>
						</ul>
					</li>

					<!-- Attendance -->
					<li class="sidebar-list {{ request()->routeIs('admin.attendance.*') || request()->routeIs('admin.staff-attendance.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.staff-leaves.index') || request()->routeIs('admin.staff-leaves.show') || request()->routeIs('admin.staff-leaves.create') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.attendance.*') || request()->routeIs('admin.staff-attendance.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.staff-leaves.index') || request()->routeIs('admin.staff-leaves.show') || request()->routeIs('admin.staff-leaves.create') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-calender') }}"></use>
							</svg>
							<span>Attendance</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.attendance.*') || request()->routeIs('admin.staff-attendance.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.staff-leaves.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.attendance.mark') ? 'active' : '' }}" href="{{ route('admin.attendance.mark') }}">Student Attendance</a></li>
							<li><a class="{{ request()->routeIs('admin.attendance.reports') ? 'active' : '' }}" href="{{ route('admin.attendance.reports') }}">Student Reports</a></li>
							<li><a class="{{ request()->routeIs('admin.attendance.calendar') ? 'active' : '' }}" href="{{ route('admin.attendance.calendar') }}">Attendance Calendar</a></li>
							<li><a class="{{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}" href="{{ route('admin.leaves.index') }}">Student Leaves</a></li>
							<li><a class="{{ request()->routeIs('admin.staff-attendance.mark') ? 'active' : '' }}" href="{{ route('admin.staff-attendance.mark') }}">Staff Attendance</a></li>
							<li><a class="{{ request()->routeIs('admin.staff-attendance.reports') ? 'active' : '' }}" href="{{ route('admin.staff-attendance.reports') }}">Staff Reports</a></li>
							<li><a class="{{ request()->routeIs('admin.staff-leaves.index') || request()->routeIs('admin.staff-leaves.show') || request()->routeIs('admin.staff-leaves.create') ? 'active' : '' }}" href="{{ route('admin.staff-leaves.index') }}">Staff Leaves</a></li>
						</ul>
					</li>

					<!-- Examinations -->
					<li class="sidebar-list">
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

					<!-- Homework -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.homework.*') ? 'active' : '' }}" href="{{ route('admin.homework.index') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-learning') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-learning') }}"></use>
							</svg>
							<span>Homework</span>
						</a>
					</li>

					<!-- Student Promotion -->
					<li class="sidebar-list {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-task') }}"></use>
							</svg>
							<span>Promotion</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.promotions.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.promotions.index') ? 'active' : '' }}" href="{{ route('admin.promotions.index') }}">Dashboard</a></li>
							<li><a class="{{ request()->routeIs('admin.promotions.create') ? 'active' : '' }}" href="{{ route('admin.promotions.create') }}">Promote Students</a></li>
							<li><a class="{{ request()->routeIs('admin.promotions.rules') ? 'active' : '' }}" href="{{ route('admin.promotions.rules') }}">Promotion Rules</a></li>
							<li><a class="{{ request()->routeIs('admin.promotions.history') ? 'active' : '' }}" href="{{ route('admin.promotions.history') }}">History</a></li>
						</ul>
					</li>

					<!-- Finance -->
					<li class="sidebar-main-title">
						<div><h6>Finance</h6></div>
					</li>

					<!-- Fees -->
					<li class="sidebar-list {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-ecommerce') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-ecommerce') }}"></use>
							</svg>
							<span>Fees</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.fees.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.fees.types.*') ? 'active' : '' }}" href="{{ route('admin.fees.types.index') }}">Fee Types</a></li>
							<li><a class="{{ request()->routeIs('admin.fees.groups.*') ? 'active' : '' }}" href="{{ route('admin.fees.groups.index') }}">Fee Groups</a></li>
							<li><a class="{{ request()->routeIs('admin.fees.structure*') && !request()->routeIs('admin.fees.structure.*') ? 'active' : '' }}" href="{{ route('admin.fees.structure') }}">Fee Structure</a></li>
							<li><a class="{{ request()->routeIs('admin.fees.collection*') || request()->routeIs('admin.fees.collect') ? 'active' : '' }}" href="{{ route('admin.fees.collection') }}">Collect Fees</a></li>
							<li><a class="{{ request()->routeIs('admin.fees.outstanding') ? 'active' : '' }}" href="{{ route('admin.fees.outstanding') }}">Outstanding</a></li>
							<li><a class="{{ request()->routeIs('admin.fees.discounts.*') ? 'active' : '' }}" href="{{ route('admin.fees.discounts.index') }}">Discounts</a></li>
							<li><a class="{{ request()->routeIs('admin.fees.reports.*') ? 'active' : '' }}" href="{{ route('admin.fees.reports.index') }}">Reports & Analytics</a></li>
							<li><a class="{{ request()->routeIs('admin.fees.reconciliation.*') ? 'active' : '' }}" href="{{ route('admin.fees.reconciliation.index') }}">Reconciliation</a></li>
						</ul>
					</li>

					<!-- Communication -->
					<li class="sidebar-main-title">
						<div><h6>Communication</h6></div>
					</li>

					<!-- Notices & Events -->
					<li class="sidebar-list {{ request()->routeIs('admin.notices.*') || request()->routeIs('admin.events.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.notices.*') || request()->routeIs('admin.events.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-form') }}"></use>
							</svg>
							<span>Notices & Events</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.notices.*') || request()->routeIs('admin.events.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.notices.*') ? 'active' : '' }}" href="{{ route('admin.notices.index') }}">Notices</a></li>
							<li><a class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}" href="{{ route('admin.events.index') }}">Events</a></li>
						</ul>
					</li>

					<!-- Messaging -->
					<li class="sidebar-list {{ request()->routeIs('admin.messaging.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.messaging.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-email') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-email') }}"></use>
							</svg>
							<span>Messaging</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.messaging.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.messaging.inbox.*') ? 'active' : '' }}" href="{{ route('admin.messaging.inbox.index') }}">Inbox</a></li>
							<li><a class="{{ request()->routeIs('admin.messaging.bulk.*') ? 'active' : '' }}" href="{{ route('admin.messaging.bulk.index') }}">Bulk Messages</a></li>
						</ul>
					</li>

					<!-- Facilities -->
					<li class="sidebar-main-title">
						<div><h6>Facilities</h6></div>
					</li>

					<!-- Library -->
					<li class="sidebar-list {{ request()->routeIs('admin.library.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.library.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-bookmark') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-bookmark') }}"></use>
							</svg>
							<span>Library</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.library.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.library.categories.*') ? 'active' : '' }}" href="{{ route('admin.library.categories.index') }}">Categories</a></li>
							<li><a class="{{ request()->routeIs('admin.library.books.*') ? 'active' : '' }}" href="{{ route('admin.library.books.index') }}">Books</a></li>
							<li><a class="{{ request()->routeIs('admin.library.issue.*') ? 'active' : '' }}" href="{{ route('admin.library.issue.index') }}">Issue/Return</a></li>
							<li><a class="{{ request()->routeIs('admin.library.reports.*') ? 'active' : '' }}" href="{{ route('admin.library.reports.index') }}">Reports</a></li>
						</ul>
					</li>

					<!-- Transport -->
					<li class="sidebar-list {{ request()->routeIs('admin.transport.*') || request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.transport.*') || request()->routeIs('admin.drivers.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-maps') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-maps') }}"></use>
							</svg>
							<span>Transport</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.transport.*') || request()->routeIs('admin.drivers.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.transport.vehicles.*') ? 'active' : '' }}" href="{{ route('admin.transport.vehicles.index') }}">Vehicles</a></li>
							<li><a class="{{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}" href="{{ route('admin.drivers.index') }}">Drivers</a></li>
							<li><a class="{{ request()->routeIs('admin.transport.routes.*') ? 'active' : '' }}" href="{{ route('admin.transport.routes.index') }}">Routes</a></li>
							<li><a class="{{ request()->routeIs('admin.transport.assignments.*') ? 'active' : '' }}" href="{{ route('admin.transport.assignments.index') }}">Assign Students</a></li>
							<li><a class="{{ request()->routeIs('admin.transport.fees.*') ? 'active' : '' }}" href="{{ route('admin.transport.fees.index') }}">Transport Fees</a></li>
							<li><a class="{{ request()->routeIs('admin.transport.reports.*') ? 'active' : '' }}" href="{{ route('admin.transport.reports.index') }}">Reports</a></li>
						</ul>
					</li>

					<!-- Administration -->
					<li class="sidebar-main-title">
						<div><h6>Administration</h6></div>
					</li>

					<!-- Users & Staff -->
					<li class="sidebar-list {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.staff.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.staff.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
							</svg>
							<span>Users & Staff</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.users.*') || request()->routeIs('admin.staff.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">All Users</a></li>
							<li><a class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">Add User</a></li>
							<li><a class="{{ request()->routeIs('admin.staff.index') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">All Staff</a></li>
							<li><a class="{{ request()->routeIs('admin.staff.create') ? 'active' : '' }}" href="{{ route('admin.staff.create') }}">Add Staff</a></li>
						</ul>
					</li>

					<!-- Departments & Designations -->
					<li class="sidebar-list {{ request()->routeIs('admin.departments.*') || request()->routeIs('admin.designations.*') || request()->routeIs('admin.staff-leaves.types.*') || request()->routeIs('admin.staff-leaves.balances*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.departments.*') || request()->routeIs('admin.designations.*') || request()->routeIs('admin.staff-leaves.types.*') || request()->routeIs('admin.staff-leaves.balances*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-sitemap') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-sitemap') }}"></use>
							</svg>
							<span>HR Setup</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.departments.*') || request()->routeIs('admin.designations.*') || request()->routeIs('admin.staff-leaves.types.*') || request()->routeIs('admin.staff-leaves.balances*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">Departments</a></li>
							<li><a class="{{ request()->routeIs('admin.designations.*') ? 'active' : '' }}" href="{{ route('admin.designations.index') }}">Designations</a></li>
							<li><a class="{{ request()->routeIs('admin.staff-leaves.types.*') ? 'active' : '' }}" href="{{ route('admin.staff-leaves.types.index') }}">Leave Types</a></li>
							<li><a class="{{ request()->routeIs('admin.staff-leaves.balances*') ? 'active' : '' }}" href="{{ route('admin.staff-leaves.balances') }}">Leave Balances</a></li>
						</ul>
					</li>

					<!-- Website Management -->
					<li class="sidebar-list {{ request()->routeIs('admin.website.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.website.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-blog') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-blog') }}"></use>
							</svg>
							<span>Website</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.website.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('admin.website.index') ? 'active' : '' }}" href="{{ route('admin.website.index') }}">Dashboard</a></li>
							<li><a class="{{ request()->routeIs('admin.website.sliders*') ? 'active' : '' }}" href="{{ route('admin.website.sliders') }}">Sliders</a></li>
							<li><a class="{{ request()->routeIs('admin.website.facilities*') ? 'active' : '' }}" href="{{ route('admin.website.facilities') }}">Facilities</a></li>
							<li><a class="{{ request()->routeIs('admin.website.testimonials*') ? 'active' : '' }}" href="{{ route('admin.website.testimonials') }}">Testimonials</a></li>
							<li><a class="{{ request()->routeIs('admin.website.gallery*') ? 'active' : '' }}" href="{{ route('admin.website.gallery') }}">Gallery</a></li>
							<li><a class="{{ request()->routeIs('admin.website.pages*') ? 'active' : '' }}" href="{{ route('admin.website.pages') }}">Pages</a></li>
							<li><a class="{{ request()->routeIs('admin.website.contacts*') ? 'active' : '' }}" href="{{ route('admin.website.contacts') }}">Messages</a></li>
						</ul>
					</li>

					<!-- Settings -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#setting') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#setting') }}"></use>
							</svg>
							<span>Settings</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('admin.settings*') ? 'display: block;' : '' }}">
							<li>
								<a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">School Settings</a>
							</li>
							<li>
								<a href="{{ route('admin.settings.library') }}" class="{{ request()->routeIs('admin.settings.library*') ? 'active' : '' }}">Library Settings</a>
							</li>
							<li>
								<a href="{{ route('admin.settings.payment') }}" class="{{ request()->routeIs('admin.settings.payment*') ? 'active' : '' }}">Payment Gateway</a>
							</li>
							<li>
								<a href="{{ route('admin.settings.sms.index') }}" class="{{ request()->routeIs('admin.settings.sms*') ? 'active' : '' }}">SMS Settings</a>
							</li>
						</ul>
					</li>

				</ul>
			</div>
			<div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
		</nav>
	</div>
</div>
