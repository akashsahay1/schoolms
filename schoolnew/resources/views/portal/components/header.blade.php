@php
    $student = \App\Models\Student::where('user_id', Auth::id())->first();
    $photoUrl = $student ? $student->photo_url : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=7366ff&color=fff&size=40';
    $photoUrl60 = $student ? $student->photo_url : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=7366ff&color=fff&size=60';
@endphp
<div class="page-header">
    <div class="header-wrapper row m-0">
        <div class="header-logo-wrapper col-auto p-0">
            <div class="toggle-sidebar" id="sidebar-toggle-btn">
                <svg class="stroke-icon sidebar-toggle status_toggle middle" style="color: #2c323f; stroke: #2c323f;">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                </svg>
            </div>
        </div>
        <div class="left-header col horizontal-wrapper ps-0 d-none d-md-flex align-items-center">
            <span class="badge bg-light text-primary">Student Portal</span>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                <!-- Notifications -->
                <li class="onhover-dropdown">
                    <div class="notification-box">
                        <svg style="stroke: #2c323f;">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>
                        </svg>
                        <span class="badge rounded-pill badge-success">0</span>
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
                        <img class="b-r-10" src="{{ $photoUrl }}" alt="{{ Auth::user()->name }}" width="40" height="40" style="border-radius: 10px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=7366ff&color=fff&size=40'">
                        <div class="flex-grow-1 user">
                            <span style="color: #2c323f !important;">{{ Auth::user()->name }}</span>
                            <p class="mb-0" style="color: #6c757d !important;">Student<i class="middle fa fa-angle-down" style="color: #2c323f;"></i></p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li><a href="{{ route('portal.profile') }}"><i data-feather="user"></i><span>My Profile</span></a></li>
                        <li><a href="{{ route('portal.fees.overview') }}"><i data-feather="credit-card"></i><span>Fee Overview</span></a></li>
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('portal-logout-form').submit();"><i data-feather="log-out"></i><span>Log out</span></a></li>
                    </ul>
                    <form method="POST" action="{{ route('logout') }}" id="portal-logout-form" style="display: none;">@csrf</form>
                </li>
            </ul>
        </div>
    </div>
</div>
