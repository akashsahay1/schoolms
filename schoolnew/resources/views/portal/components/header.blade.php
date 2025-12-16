<div class="page-header">
    <div class="header-wrapper row m-0">
        <div class="header-logo-wrapper col-auto p-0">
            <div class="toggle-sidebar" id="sidebar-toggle-btn">
                <svg class="stroke-icon sidebar-toggle status_toggle middle">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                </svg>
            </div>
        </div>
        <div class="left-header col horizontal-wrapper ps-0 d-none d-md-flex align-items-center">
            <span class="badge bg-primary">Student Portal</span>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                <!-- Notifications -->
                <li class="onhover-dropdown">
                    <div class="notification-box">
                        <svg>
                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-notification') }}"></use>
                        </svg>
                    </div>
                    <div class="onhover-show-div notification-dropdown">
                        <h5 class="mb-0 f-14 dropdown-title">Notifications</h5>
                        <ul>
                            <li class="b-l-primary border-4">
                                <p class="font-primary">No new notifications</p>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- User Profile -->
                <li class="profile-nav onhover-dropdown pe-0 py-0 me-0">
                    <div class="d-flex align-items-center profile-media">
                        <img class="b-r-10 img-40" src="{{ Auth::user()->avatar_url }}" alt="">
                        <div class="flex-grow-1 user">
                            <span>{{ Auth::user()->name }}</span>
                            <p class="mb-0">Student<i class="middle fa fa-angle-down"></i></p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li>
                            <a href="{{ route('portal.profile') }}">
                                <i data-feather="user"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('portal.fees.overview') }}">
                                <i data-feather="credit-card"></i>
                                <span>Fee Overview</span>
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                @csrf
                                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
