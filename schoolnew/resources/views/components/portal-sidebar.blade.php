@php
	$user = Auth::user();
	$isParent = false;
	$isStudent = false;
	$student = null;
	$children = [];
	$selectedChild = null;
	$parent = null;

	// Check if user is a student
	$student = \App\Models\Student::where('user_id', $user->id)->first();
	if ($student) {
		$isStudent = true;
		$selectedChild = $student;
	} else {
		// Check if user is a parent by user_id OR by email
		$parent = \App\Models\ParentGuardian::where('user_id', $user->id)->first();

		// If not found by user_id, try by email
		if (!$parent) {
			$parent = \App\Models\ParentGuardian::where('father_email', $user->email)
				->orWhere('mother_email', $user->email)
				->orWhere('guardian_email', $user->email)
				->first();

			// Link the user_id if found by email (update the record)
			if ($parent && !$parent->user_id) {
				$parent->update(['user_id' => $user->id]);
			}
		}

		if ($parent) {
			$isParent = true;
			$children = \App\Models\Student::where('parent_id', $parent->id)
				->where('status', 'active')
				->with(['schoolClass', 'section'])
				->get();

			// Get selected child from session or use first child
			$selectedChildId = session('selected_child_id');
			if ($selectedChildId) {
				$selectedChild = $children->firstWhere('id', $selectedChildId);
			}
			if (!$selectedChild && $children->count() > 0) {
				$selectedChild = $children->first();
				session(['selected_child_id' => $selectedChild->id]);
			}
		}
	}
@endphp

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
						<div><h6>{{ $isParent ? 'Parent Portal' : 'Student Portal' }}</h6></div>
					</li>

					@if($isParent && $children->count() > 0)
					<!-- Child Selector for Parents -->
					<li class="sidebar-list">
						<div class="px-3 py-2">
							<label class="form-label text-white-50 small mb-1">Viewing as:</label>
							<select class="form-select form-select-sm" id="childSelector" onchange="switchChild(this.value)">
								@foreach($children as $child)
									<option value="{{ $child->id }}" {{ $selectedChild && $selectedChild->id == $child->id ? 'selected' : '' }}>
										{{ $child->full_name }} ({{ $child->schoolClass->name ?? '' }} - {{ $child->section->name ?? '' }})
									</option>
								@endforeach
							</select>
						</div>
					</li>
					@endif

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
						<a class="sidebar-link sidebar-title {{ request()->routeIs('portal.profile') ? 'active' : '' }}" href="{{ route('portal.profile') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
							</svg>
							<span>{{ $isParent ? 'Child Profile' : 'My Profile' }}</span>
						</a>
					</li>

					<!-- Academic -->
					<li class="sidebar-main-title">
						<div><h6>Academic</h6></div>
					</li>

					<!-- Attendance -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('portal.attendance') ? 'active' : '' }}" href="{{ route('portal.attendance') }}">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-calender') }}"></use>
							</svg>
							<span>{{ $isParent ? 'Attendance' : 'My Attendance' }}</span>
						</a>
					</li>

					<!-- Timetable -->
					<li class="sidebar-list">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('portal.timetable') ? 'active' : '' }}" href="{{ route('portal.timetable') }}">
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
						<a class="sidebar-link sidebar-title {{ request()->routeIs('portal.homework.*') ? 'active' : '' }}" href="{{ route('portal.homework.index') }}">
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
					<li class="sidebar-list {{ request()->routeIs('portal.exams.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('portal.exams.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-file') }}"></use>
							</svg>
							<span>Exams & Results</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('portal.exams.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('portal.exams.index') ? 'active' : '' }}" href="{{ route('portal.exams.index') }}">Exam Schedule</a></li>
							<li><a class="{{ request()->routeIs('portal.exams.results') ? 'active' : '' }}" href="{{ route('portal.exams.results') }}">Results</a></li>
							<li><a class="{{ request()->routeIs('portal.exams.report-card') ? 'active' : '' }}" href="{{ route('portal.exams.report-card') }}">Report Card</a></li>
						</ul>
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
							<li><a class="{{ request()->routeIs('portal.library.index') ? 'active' : '' }}" href="{{ route('portal.library.index') }}">Borrowed Books</a></li>
							<li><a class="{{ request()->routeIs('portal.library.search') ? 'active' : '' }}" href="{{ route('portal.library.search') }}">Search Books</a></li>
							<li><a class="{{ request()->routeIs('portal.library.history') ? 'active' : '' }}" href="{{ route('portal.library.history') }}">Borrow History</a></li>
						</ul>
					</li>

					<!-- Fees -->
					<li class="sidebar-main-title">
						<div><h6>Finance</h6></div>
					</li>

					<li class="sidebar-list {{ request()->routeIs('portal.fees.*') || request()->routeIs('portal.payment.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('portal.fees.*') || request()->routeIs('portal.payment.*') ? 'active' : '' }}" href="#">
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

					<!-- Leave Applications -->
					<li class="sidebar-list {{ request()->routeIs('portal.leaves.*') ? 'active' : '' }}">
						<a class="sidebar-link sidebar-title {{ request()->routeIs('portal.leaves.*') ? 'active' : '' }}" href="#">
							<svg class="stroke-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-editors') }}"></use>
							</svg>
							<svg class="fill-icon">
								<use href="{{ asset('assets/svg/icon-sprite.svg#fill-editors') }}"></use>
							</svg>
							<span>Leave Applications</span>
						</a>
						<ul class="sidebar-submenu" style="{{ request()->routeIs('portal.leaves.*') ? 'display: block;' : '' }}">
							<li><a class="{{ request()->routeIs('portal.leaves.index') ? 'active' : '' }}" href="{{ route('portal.leaves.index') }}">All Applications</a></li>
							<li><a class="{{ request()->routeIs('portal.leaves.create') ? 'active' : '' }}" href="{{ route('portal.leaves.create') }}">Apply for Leave</a></li>
						</ul>
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
			<div class="right-arrow" id="right-arrow">
				<i data-feather="arrow-right"></i>
			</div>
		</nav>
	</div>
</div>

@if($isParent)
<script>
function switchChild(childId) {
	// Send AJAX request to change selected child
	fetch('{{ route("portal.switch-child") }}', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
		body: JSON.stringify({ child_id: childId })
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			window.location.reload();
		}
	});
}
</script>
@endif
