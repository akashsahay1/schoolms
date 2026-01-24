<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
    <div>
        <!-- Logo -->
        <div class="logo-wrapper">
            <a href="{{ route('portal.dashboard') }}">
                <img class="img-fluid for-light logo-custom" src="{{ asset('assets/images/logo/logo.png') }}" alt="">
                <img class="img-fluid for-dark logo-custom" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="">
            </a>
            <div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
            <div class="toggle-sidebar" style="cursor: pointer;">
                <svg style="width: 20px; height: 20px;">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                </svg>
            </div>
        </div>
        <div class="logo-icon-wrapper">
            <a href="{{ route('portal.dashboard') }}">
                <img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
            </a>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn">
                        <a href="{{ route('portal.dashboard') }}">
                            <img class="img-fluid" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="">
                        </a>
                        <div class="mobile-back text-end">
                            <span>Back</span>
                            <i class="fa-solid fa-angle-right ps-2"></i>
                        </div>
                    </li>

                    <!-- Main -->
                    <li class="sidebar-main-title">
                        <div><h6>Student Portal</h6></div>
                    </li>

                    <!-- Dashboard -->
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}" href="{{ route('portal.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Profile -->
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('portal.profile') ? 'active' : '' }}" href="{{ route('portal.profile') }}">
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
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('portal.attendance') ? 'active' : '' }}" href="{{ route('portal.attendance') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-calender') }}"></use>
                            </svg>
                            <span>Attendance</span>
                        </a>
                    </li>

                    <!-- Timetable -->
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('portal.timetable') ? 'active' : '' }}" href="{{ route('portal.timetable') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-board') }}"></use>
                            </svg>
                            <span>Timetable</span>
                        </a>
                    </li>

                    <!-- Exams -->
                    <li class="sidebar-list {{ request()->routeIs('portal.exams.*') ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('portal.exams.*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-file') }}"></use>
                            </svg>
                            <span>Examinations</span>
                        </a>
                        <ul class="sidebar-submenu" style="{{ request()->routeIs('portal.exams.*') ? 'display: block;' : '' }}">
                            <li><a class="{{ request()->routeIs('portal.exams.index') ? 'active' : '' }}" href="{{ route('portal.exams.index') }}">Exam Schedule</a></li>
                            <li><a class="{{ request()->routeIs('portal.exams.results') ? 'active' : '' }}" href="{{ route('portal.exams.results') }}">My Results</a></li>
                            <li><a class="{{ request()->routeIs('portal.exams.report-card') ? 'active' : '' }}" href="{{ route('portal.exams.report-card') }}">Report Card</a></li>
                        </ul>
                    </li>

                    <!-- Homework -->
                    <li class="sidebar-list {{ request()->routeIs('portal.homework.*') ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('portal.homework.*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-task') }}"></use>
                            </svg>
                            <span>Homework</span>
                        </a>
                        <ul class="sidebar-submenu" style="{{ request()->routeIs('portal.homework.*') ? 'display: block;' : '' }}">
                            <li><a class="{{ request()->routeIs('portal.homework.index') ? 'active' : '' }}" href="{{ route('portal.homework.index') }}">All Homework</a></li>
                            <li><a class="{{ request()->routeIs('portal.homework.pending') ? 'active' : '' }}" href="{{ route('portal.homework.pending') }}">Pending</a></li>
                            <li><a class="{{ request()->routeIs('portal.homework.submitted') ? 'active' : '' }}" href="{{ route('portal.homework.submitted') }}">Submitted</a></li>
                        </ul>
                    </li>

                    <!-- Leave Applications -->
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('portal.leaves.*') ? 'active' : '' }}" href="{{ route('portal.leaves.index') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-form') }}"></use>
                            </svg>
                            <span>Leave Applications</span>
                        </a>
                    </li>

                    <!-- Library -->
                    <li class="sidebar-list {{ request()->routeIs('portal.library.*') ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('portal.library.*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-bookmark') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-bookmark') }}"></use>
                            </svg>
                            <span>Library</span>
                        </a>
                        <ul class="sidebar-submenu" style="{{ request()->routeIs('portal.library.*') ? 'display: block;' : '' }}">
                            <li><a class="{{ request()->routeIs('portal.library.index') ? 'active' : '' }}" href="{{ route('portal.library.index') }}">My Books</a></li>
                            <li><a class="{{ request()->routeIs('portal.library.search') ? 'active' : '' }}" href="{{ route('portal.library.search') }}">Search Books</a></li>
                            <li><a class="{{ request()->routeIs('portal.library.history') ? 'active' : '' }}" href="{{ route('portal.library.history') }}">Borrow History</a></li>
                        </ul>
                    </li>

                    <!-- Finance -->
                    <li class="sidebar-main-title">
                        <div><h6>Finance</h6></div>
                    </li>

                    <!-- Fees -->
                    <li class="sidebar-list {{ request()->routeIs('portal.fees.*') ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title {{ request()->routeIs('portal.fees.*') ? 'active' : '' }}" href="#">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-ecommerce') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-ecommerce') }}"></use>
                            </svg>
                            <span>Fees</span>
                        </a>
                        <ul class="sidebar-submenu" style="{{ request()->routeIs('portal.fees.*') || request()->routeIs('portal.payment.*') ? 'display: block;' : '' }}">
                            <li><a class="{{ request()->routeIs('portal.fees.overview') ? 'active' : '' }}" href="{{ route('portal.fees.overview') }}">Fee Overview</a></li>
                            <li><a class="{{ request()->routeIs('portal.payment.checkout') ? 'active' : '' }}" href="{{ route('portal.payment.checkout') }}">Pay Online</a></li>
                            <li><a class="{{ request()->routeIs('portal.fees.history') ? 'active' : '' }}" href="{{ route('portal.fees.history') }}">Payment History</a></li>
                        </ul>
                    </li>

                    <!-- Communication -->
                    <li class="sidebar-main-title">
                        <div><h6>Communication</h6></div>
                    </li>

                    <!-- Notices -->
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('portal.notices*') ? 'active' : '' }}" href="{{ route('portal.notices') }}">
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
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('portal.events*') ? 'active' : '' }}" href="{{ route('portal.events') }}">
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
                        <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('portal.contact*') ? 'active' : '' }}" href="{{ route('portal.contact') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-email') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-email') }}"></use>
                            </svg>
                            <span>Contact School</span>
                        </a>
                    </li>

                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
