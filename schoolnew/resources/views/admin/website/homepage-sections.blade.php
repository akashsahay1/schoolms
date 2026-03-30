@extends('layouts.app')

@section('title', 'Edit Homepage')

@section('page-title', 'Edit Homepage')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item active">Homepage</li>
@endsection

@push('styles')
<style>
.section-accordion .acc-header {
    padding: 14px 20px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    transition: background 0.2s;
    user-select: none;
}
.section-accordion .acc-header:hover { background: #f8f9fa; }
.section-accordion .acc-header .left { display: flex; align-items: center; gap: 12px; }
.section-accordion .acc-header .num {
    width: 30px; height: 30px; border-radius: 50%; color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; flex-shrink: 0;
}
.section-accordion .acc-header h6 { margin: 0; font-size: 14px; font-weight: 600; }
.section-accordion .acc-header small { color: #999; font-size: 11px; }
.section-accordion .acc-header .toggle-icon { font-size: 18px; color: #aaa; transition: transform 0.3s; }
.section-accordion .acc-header.open .toggle-icon { transform: rotate(180deg); }
.section-accordion .acc-body { display: none; padding: 20px; border-bottom: 1px solid #eee; }
.section-accordion .acc-body.open { display: block; }
.other-section-link {
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    transition: background 0.2s;
    text-decoration: none;
    color: inherit;
}
.other-section-link:hover { background: #f0efff; color: var(--theme-default); }
.other-section-link .left { display: flex; align-items: center; gap: 12px; }
.other-section-link .num {
    width: 30px; height: 30px; border-radius: 50%; background: #e9ecef; color: #666;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; flex-shrink: 0;
}
.other-section-link .go-arrow { color: #aaa; font-size: 16px; }
.other-section-link:hover .go-arrow { color: var(--theme-default); }
</style>
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
        <i class="icon-check me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
    </div>
@endif

<!-- Top bar -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <p class="text-muted mb-0" style="font-size: 13px;">Click on a section below to expand and edit it. Grayed sections are managed on their own page.</p>
    </div>
    <a href="{{ route('website.home') }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="icon-eye me-1"></i> View Live Homepage</a>
</div>

<form action="{{ route('admin.website.update-homepage-sections') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card section-accordion mb-0" style="border-radius: 10px; overflow: hidden;">

        <!-- 1. Hero Slider (link only) -->
        <a href="{{ route('admin.website.sliders') }}" class="other-section-link">
            <div class="left">
                <span class="num" style="background: #6c757d; color: #fff;">1</span>
                <div>
                    <h6 style="font-size: 14px; margin: 0; font-weight: 600;">Hero Slider</h6>
                    <small style="color: #999; font-size: 11px;">Big banner images at top of page</small>
                </div>
            </div>
            <span class="go-arrow"><i class="icon-arrow-right"></i></span>
        </a>

        <!-- 2. Why Choose Us (editable) -->
        <div class="acc-header" data-target="acc-why">
            <div class="left">
                <span class="num" style="background: #7366ff;">2</span>
                <div>
                    <h6>Why Choose Us</h6>
                    <small>4 feature cards with icons</small>
                </div>
            </div>
            <i class="icon-angle-down toggle-icon"></i>
        </div>
        <div class="acc-body" id="acc-why">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Section Title</label>
                    <input type="text" name="homepage_why_title" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_title', 'Why Choose Us') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Subtitle</label>
                    <input type="text" name="homepage_why_subtitle" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_subtitle', 'Discover what makes our school the perfect place for your child\'s education') }}">
                </div>
            </div>
            @for($i = 1; $i <= 4; $i++)
            <div class="p-3 bg-light rounded mb-2">
                <div class="row g-2 align-items-center">
                    <div class="col-auto"><span class="badge bg-primary rounded-pill">Card {{ $i }}</span></div>
                    <div class="col-md-2">
                        <input type="text" name="homepage_why_{{ $i }}_icon" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('homepage_why_'.$i.'_icon', ['book-open','users','award','heart'][$i-1]) }}" placeholder="Icon name">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="homepage_why_{{ $i }}_title" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('homepage_why_'.$i.'_title', ['Quality Education','Expert Faculty','Modern Facilities','Safe Environment'][$i-1]) }}" placeholder="Title">
                    </div>
                    <div class="col">
                        <input type="text" name="homepage_why_{{ $i }}_desc" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('homepage_why_'.$i.'_desc', '') }}" placeholder="Short description">
                    </div>
                </div>
            </div>
            @endfor
            <small class="text-muted"><i class="icon-info-alt me-1"></i> Icon names from <a href="https://feathericons.com" target="_blank">feathericons.com</a></small>
        </div>

        <!-- 3. About Us (editable) -->
        <div class="acc-header" data-target="acc-about">
            <div class="left">
                <span class="num" style="background: #54BA4A;">3</span>
                <div>
                    <h6>About Us</h6>
                    <small>Image, description & checklist</small>
                </div>
            </div>
            <i class="icon-angle-down toggle-icon"></i>
        </div>
        <div class="acc-body" id="acc-about">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Section Image</label>
                    @php $aboutImage = \App\Models\Setting::get('homepage_about_image'); @endphp
                    @if($aboutImage)
                        <div class="mb-2"><img src="{{ asset('storage/' . $aboutImage) }}" class="img-thumbnail" style="max-height: 100px;"></div>
                    @endif
                    <input type="file" name="homepage_about_image" class="form-control form-control-sm" accept="image/*">
                    <small class="text-muted">600 x 450px recommended</small>
                </div>
                <div class="col-md-8">
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label fw-bold">Subtitle</label>
                            <input type="text" name="homepage_about_subtitle" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('homepage_about_subtitle', 'ABOUT US') }}">
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" name="homepage_about_title" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('homepage_about_title', 'A Legacy of Educational Excellence') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="homepage_about_description" class="form-control form-control-sm" rows="3">{{ \App\Models\Setting::get('homepage_about_description', 'Our school has been a beacon of educational excellence for over two decades.') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <label class="form-label fw-bold">Checklist Items</label>
            <div class="row g-2">
                @for($i = 1; $i <= 6; $i++)
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-success text-white"><i class="icon-check" style="color: #fff;"></i></span>
                        <input type="text" name="homepage_about_check_{{ $i }}" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_check_'.$i, ['Experienced Teachers','Modern Curriculum','Character Building','Sports Activities','Smart Classes','Safe Environment'][$i-1]) }}">
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- 4. Statistics (editable) -->
        <div class="acc-header" data-target="acc-stats">
            <div class="left">
                <span class="num" style="background: #FFAA05;">4</span>
                <div>
                    <h6>Statistics Counters</h6>
                    <small>Students, Teachers, Years, Awards numbers</small>
                </div>
            </div>
            <i class="icon-angle-down toggle-icon"></i>
        </div>
        <div class="acc-body" id="acc-stats">
            <div class="row g-3">
                @php
                    $stats = [
                        ['key' => 'total_students', 'lkey' => 'stat_1_label', 'num' => '1500', 'label' => 'Students'],
                        ['key' => 'total_teachers', 'lkey' => 'stat_2_label', 'num' => '100', 'label' => 'Teachers'],
                        ['key' => 'school_years', 'lkey' => 'stat_3_label', 'num' => '25', 'label' => 'Years'],
                        ['key' => 'awards_count', 'lkey' => 'stat_4_label', 'num' => '50', 'label' => 'Awards'],
                    ];
                @endphp
                @foreach($stats as $stat)
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <label class="form-label fw-bold" style="font-size: 12px;">Number</label>
                        <input type="text" name="{{ $stat['key'] }}" class="form-control text-center fw-bold mb-2" value="{{ \App\Models\Setting::get($stat['key'], $stat['num']) }}" style="font-size: 20px;">
                        <label class="form-label" style="font-size: 12px;">Label</label>
                        <input type="text" name="{{ $stat['lkey'] }}" class="form-control form-control-sm text-center" value="{{ \App\Models\Setting::get($stat['lkey'], $stat['label']) }}">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 5. Facilities (link only) -->
        <a href="{{ route('admin.website.facilities') }}" class="other-section-link">
            <div class="left">
                <span class="num">5</span>
                <div>
                    <h6 style="font-size: 14px; margin: 0; font-weight: 600;">Facilities</h6>
                    <small style="color: #999; font-size: 11px;">6 facility cards with icons</small>
                </div>
            </div>
            <span class="go-arrow"><i class="icon-arrow-right"></i></span>
        </a>

        <!-- 6. Events & News (link only) -->
        <div style="padding: 12px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <div class="d-flex align-items-center gap-3">
                <span style="width: 30px; height: 30px; border-radius: 50%; background: #e9ecef; color: #666; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">6</span>
                <div>
                    <h6 style="font-size: 14px; margin: 0; font-weight: 600;">Events & News</h6>
                    <small style="color: #999; font-size: 11px;">Auto-pulled from Events and Notices</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-sm" style="font-size: 11px;">Events</a>
                <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-secondary btn-sm" style="font-size: 11px;">Notices</a>
            </div>
        </div>

        <!-- 7. Gallery (link only) -->
        <a href="{{ route('admin.website.gallery') }}" class="other-section-link">
            <div class="left">
                <span class="num">7</span>
                <div>
                    <h6 style="font-size: 14px; margin: 0; font-weight: 600;">Photo Gallery</h6>
                    <small style="color: #999; font-size: 11px;">8 photos in a grid</small>
                </div>
            </div>
            <span class="go-arrow"><i class="icon-arrow-right"></i></span>
        </a>

        <!-- 8. Testimonials (link only) -->
        <a href="{{ route('admin.website.testimonials') }}" class="other-section-link">
            <div class="left">
                <span class="num">8</span>
                <div>
                    <h6 style="font-size: 14px; margin: 0; font-weight: 600;">Testimonials</h6>
                    <small style="color: #999; font-size: 11px;">Parent & student reviews</small>
                </div>
            </div>
            <span class="go-arrow"><i class="icon-arrow-right"></i></span>
        </a>

        <!-- 9. Call to Action (editable) -->
        <div class="acc-header" data-target="acc-cta">
            <div class="left">
                <span class="num" style="background: #FC4438;">9</span>
                <div>
                    <h6>Call to Action</h6>
                    <small>"Ready to Join" banner at bottom</small>
                </div>
            </div>
            <i class="icon-angle-down toggle-icon"></i>
        </div>
        <div class="acc-body" id="acc-cta">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Heading</label>
                    <input type="text" name="cta_heading" class="form-control" value="{{ \App\Models\Setting::get('cta_heading', 'Ready to Join Our School?') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Subtitle</label>
                    <input type="text" name="cta_subtitle" class="form-control" value="{{ \App\Models\Setting::get('cta_subtitle', 'Take the first step towards your child\'s bright future') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Button Text</label>
                    <input type="text" name="cta_button_text" class="form-control" value="{{ \App\Models\Setting::get('cta_button_text', 'Contact Us Today') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Button Link</label>
                    <input type="text" name="cta_button_link" class="form-control" value="{{ \App\Models\Setting::get('cta_button_link', '') }}" placeholder="Leave empty = Contact page">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Background Image</label>
                    <input type="file" name="cta_bg_image" class="form-control form-control-sm" accept="image/*">
                    @php $ctaBg = \App\Models\Setting::get('cta_bg_image'); @endphp
                    @if($ctaBg)
                        <small class="text-success">Current: <a href="{{ asset('storage/' . $ctaBg) }}" target="_blank">View</a></small>
                    @else
                        <small class="text-muted">1920 x 400px recommended</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer (link) -->
        <a href="{{ route('admin.settings.school') }}" class="other-section-link" style="border-bottom: none;">
            <div class="left">
                <span class="num">10</span>
                <div>
                    <h6 style="font-size: 14px; margin: 0; font-weight: 600;">Footer</h6>
                    <small style="color: #999; font-size: 11px;">School name, address, phone, email</small>
                </div>
            </div>
            <span class="go-arrow"><i class="icon-arrow-right"></i></span>
        </a>
    </div>

    <!-- Save -->
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <a href="{{ route('admin.website.index') }}" class="btn btn-outline-secondary"><i class="icon-arrow-left me-1"></i> Back</a>
        <button type="submit" class="btn btn-primary px-5"><i class="icon-check me-1"></i> Save Changes</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Accordion toggle
    jQuery('.acc-header').on('click', function() {
        var target = jQuery('#' + jQuery(this).data('target'));
        var isOpen = target.hasClass('open');

        // Close all
        jQuery('.acc-body').removeClass('open');
        jQuery('.acc-header').removeClass('open');

        // Toggle clicked
        if (!isOpen) {
            target.addClass('open');
            jQuery(this).addClass('open');
            jQuery('html, body').animate({ scrollTop: jQuery(this).offset().top - 80 }, 300);
        }
    });

    @if(session('success'))
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session("success") }}', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    @endif
});
</script>
@endpush
