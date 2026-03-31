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
/* Hero banner */
.homepage-hero {
    background: linear-gradient(135deg, #7366ff 0%, #a389ff 50%, #7366ff 100%);
    border-radius: 14px;
    padding: 28px 30px;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
}
.homepage-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
}
.homepage-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: 10%;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
}
.homepage-hero * { position: relative; z-index: 2; }
.homepage-hero h4 { font-weight: 700; margin-bottom: 4px; }
.homepage-hero p { opacity: 0.85; margin-bottom: 0; font-size: 14px; }

/* Section cards */
.section-card {
    border-radius: 12px;
    border: 1px solid #eee;
    overflow: hidden;
    margin-bottom: 16px;
    transition: box-shadow 0.25s, border-color 0.25s;
    background: #fff;
}
.section-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border-color: #d0cef7;
}
.section-card .card-top {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background 0.2s;
    user-select: none;
}
.section-card .card-top:hover { background: #fafafe; }
.section-card .card-top .left { display: flex; align-items: center; gap: 14px; }
.section-card .card-top .num {
    width: 38px; height: 38px; border-radius: 10px; color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 15px; flex-shrink: 0;
}
.section-card .card-top h6 { margin: 0; font-size: 15px; font-weight: 600; color: #2c323f; }
.section-card .card-top small { color: #999; font-size: 12px; display: block; margin-top: 1px; }
.section-card .card-top .chevron {
    width: 28px; height: 28px; border-radius: 50%; background: #f3f3f8;
    display: inline-flex; align-items: center; justify-content: center;
    transition: transform 0.3s, background 0.2s;
    color: #999;
}
.section-card .card-top:hover .chevron { background: #e8e6ff; color: #7366ff; }
.section-card .card-top.open .chevron { transform: rotate(180deg); background: #7366ff; color: #fff; }
.section-card .card-body-inner { display: none; padding: 0 20px 20px; }
.section-card .card-body-inner.open { display: block; }

/* Link cards */
.link-card {
    border-radius: 12px;
    border: 1px solid #eee;
    overflow: hidden;
    margin-bottom: 16px;
    transition: box-shadow 0.25s, border-color 0.25s;
    background: #fff;
}
.link-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border-color: #d0cef7;
    text-decoration: none;
}
.link-card a {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-decoration: none;
    color: inherit;
}
.link-card a .left { display: flex; align-items: center; gap: 14px; }
.link-card a .num {
    width: 38px; height: 38px; border-radius: 10px; background: #f3f3f8; color: #888;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 15px; flex-shrink: 0;
    transition: background 0.2s;
}
.link-card:hover a .num { background: #e8e6ff; color: #7366ff; }
.link-card a h6 { margin: 0; font-size: 15px; font-weight: 600; color: #2c323f; }
.link-card a small { color: #999; font-size: 12px; display: block; margin-top: 1px; }
.link-card a .go-icon {
    width: 28px; height: 28px; border-radius: 50%; background: #f3f3f8;
    display: inline-flex; align-items: center; justify-content: center;
    color: #bbb; transition: all 0.2s;
}
.link-card:hover a .go-icon { background: #7366ff; color: #fff; }

/* Feature card mini */
.feature-mini {
    background: #f8f9fc;
    border: 1px solid #eef0f6;
    border-radius: 10px;
    padding: 14px;
    transition: border-color 0.2s;
}
.feature-mini:hover { border-color: #d0cef7; }

/* Stat counter card */
.stat-counter-card {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef0f6 100%);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    border: 1px solid #eee;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.stat-counter-card:hover { border-color: #d0cef7; box-shadow: 0 2px 12px rgba(115,102,255,0.08); }

/* Divider label */
.divider-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 20px 0 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #bbb;
}
.divider-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #eee;
}
</style>
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
        <i class="icon-check me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
    </div>
@endif

<!-- Hero Banner -->
<div class="homepage-hero">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 style="color: #fff;">Homepage Editor</h4>
            <p style="color: rgba(255,255,255,0.85);">Edit your school website homepage. Click any section to expand and change its content.</p>
        </div>
        <a href="{{ route('website.home') }}" target="_blank" class="btn btn-sm px-3" style="font-weight: 600; border-radius: 8px; color: #fff; border: 1px solid rgba(255,255,255,0.5); background: rgba(255,255,255,0.15);">
            <i class="icon-eye me-1" style="color: #fff;"></i> View Live Homepage
        </a>
    </div>
</div>

<form action="{{ route('admin.website.update-homepage-sections') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- 1. Hero Slider (link) -->
    <div class="link-card">
        <a href="{{ route('admin.website.sliders') }}">
            <div class="left">
                <span class="num" style="background: #6c757d; color: #fff;">1</span>
                <div>
                    <h6>Hero Slider</h6>
                    <small>Big banner images at the top of your homepage</small>
                </div>
            </div>
            <span class="go-icon"><i class="icon-arrow-right"></i></span>
        </a>
    </div>

    <!-- 2. Why Choose Us -->
    <div class="section-card" id="section-why">
        <div class="card-top" data-target="body-why">
            <div class="left">
                <span class="num" style="background: #7366ff;">2</span>
                <div>
                    <h6>Why Choose Us</h6>
                    <small>4 feature cards — shown below the slider</small>
                </div>
            </div>
            <span class="chevron"><i class="icon-angle-down"></i></span>
        </div>
        <div class="card-body-inner" id="body-why">
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
            <div class="divider-label">Feature Cards</div>
            <div class="row g-3">
                @for($i = 1; $i <= 4; $i++)
                <div class="col-md-6">
                    <div class="feature-mini">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary rounded-pill" style="font-size: 11px;">Card {{ $i }}</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label" style="font-size: 11px; color: #888;">Icon Name</label>
                                <input type="text" name="homepage_why_{{ $i }}_icon" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('homepage_why_'.$i.'_icon', ['book-open','users','award','heart'][$i-1]) }}">
                            </div>
                            <div class="col-8">
                                <label class="form-label" style="font-size: 11px; color: #888;">Title</label>
                                <input type="text" name="homepage_why_{{ $i }}_title" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('homepage_why_'.$i.'_title', ['Quality Education','Expert Faculty','Modern Facilities','Safe Environment'][$i-1]) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size: 11px; color: #888;">Description</label>
                                <input type="text" name="homepage_why_{{ $i }}_desc" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('homepage_why_'.$i.'_desc', '') }}" placeholder="Short description...">
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
            <div class="mt-2"><small class="text-muted"><i class="icon-info-alt me-1"></i> Icons from <a href="https://feathericons.com" target="_blank">feathericons.com</a> (e.g. book-open, users, award)</small></div>
        </div>
    </div>

    <!-- 3. About Us -->
    <div class="section-card" id="section-about">
        <div class="card-top" data-target="body-about">
            <div class="left">
                <span class="num" style="background: #54BA4A;">3</span>
                <div>
                    <h6>About Us</h6>
                    <small>Image, description & checklist items</small>
                </div>
            </div>
            <span class="chevron"><i class="icon-angle-down"></i></span>
        </div>
        <div class="card-body-inner" id="body-about">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Section Image</label>
                    @php $aboutImage = \App\Models\Setting::get('homepage_about_image'); @endphp
                    @if($aboutImage)
                        <div class="mb-2 p-2 bg-light rounded text-center">
                            <img src="{{ asset('storage/' . $aboutImage) }}" class="rounded" style="max-height: 100px;">
                        </div>
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
            <div class="divider-label">Checklist Items</div>
            <div class="row g-2">
                @for($i = 1; $i <= 6; $i++)
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text" style="background: #54BA4A; color: #fff; border: none;"><i class="icon-check" style="color: #fff;"></i></span>
                        <input type="text" name="homepage_about_check_{{ $i }}" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_check_'.$i, ['Experienced Teachers','Modern Curriculum','Character Building','Sports Activities','Smart Classes','Safe Environment'][$i-1]) }}">
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- 4. Statistics -->
    <div class="section-card" id="section-stats">
        <div class="card-top" data-target="body-stats">
            <div class="left">
                <span class="num" style="background: #FFAA05;">4</span>
                <div>
                    <h6>Statistics Counters</h6>
                    <small>Students, Teachers, Years, Awards numbers</small>
                </div>
            </div>
            <span class="chevron"><i class="icon-angle-down"></i></span>
        </div>
        <div class="card-body-inner" id="body-stats">
            <div class="row g-3">
                @php
                    $stats = [
                        ['key' => 'total_students', 'lkey' => 'stat_1_label', 'num' => '1500', 'label' => 'Students', 'color' => '#7366ff'],
                        ['key' => 'total_teachers', 'lkey' => 'stat_2_label', 'num' => '100', 'label' => 'Teachers', 'color' => '#54BA4A'],
                        ['key' => 'school_years', 'lkey' => 'stat_3_label', 'num' => '25', 'label' => 'Years', 'color' => '#FFAA05'],
                        ['key' => 'awards_count', 'lkey' => 'stat_4_label', 'num' => '50', 'label' => 'Awards', 'color' => '#FC4438'],
                    ];
                @endphp
                @foreach($stats as $stat)
                <div class="col-md-3">
                    <div class="stat-counter-card">
                        <input type="text" name="{{ $stat['key'] }}" class="form-control text-center fw-bold border-0 bg-transparent mb-1" value="{{ \App\Models\Setting::get($stat['key'], $stat['num']) }}" style="font-size: 28px; color: {{ $stat['color'] }};">
                        <input type="text" name="{{ $stat['lkey'] }}" class="form-control form-control-sm text-center border-0 bg-transparent" value="{{ \App\Models\Setting::get($stat['lkey'], $stat['label']) }}" style="color: #888;">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 5. Facilities (link) -->
    <div class="link-card">
        <a href="{{ route('admin.website.facilities') }}">
            <div class="left">
                <span class="num">5</span>
                <div>
                    <h6>Facilities</h6>
                    <small>6 facility cards with icons</small>
                </div>
            </div>
            <span class="go-icon"><i class="icon-arrow-right"></i></span>
        </a>
    </div>

    <!-- 6. Events & News (link) -->
    <div class="link-card">
        <div style="padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
            <div class="d-flex align-items-center gap-3">
                <span style="width: 38px; height: 38px; border-radius: 10px; background: #f3f3f8; color: #888; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px;">6</span>
                <div>
                    <h6 style="font-size: 15px; margin: 0; font-weight: 600; color: #2c323f;">Events & News</h6>
                    <small style="color: #999; font-size: 12px;">Auto-pulled from Events & Notices modules</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary btn-sm" style="font-size: 11px; border-radius: 6px;">Events</a>
                <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-primary btn-sm" style="font-size: 11px; border-radius: 6px;">Notices</a>
            </div>
        </div>
    </div>

    <!-- 7. Gallery (link) -->
    <div class="link-card">
        <a href="{{ route('admin.website.gallery') }}">
            <div class="left">
                <span class="num">7</span>
                <div>
                    <h6>Photo Gallery</h6>
                    <small>8 photos in a grid layout</small>
                </div>
            </div>
            <span class="go-icon"><i class="icon-arrow-right"></i></span>
        </a>
    </div>

    <!-- 8. Testimonials (link) -->
    <div class="link-card">
        <a href="{{ route('admin.website.testimonials') }}">
            <div class="left">
                <span class="num">8</span>
                <div>
                    <h6>Testimonials</h6>
                    <small>Parent & student reviews</small>
                </div>
            </div>
            <span class="go-icon"><i class="icon-arrow-right"></i></span>
        </a>
    </div>

    <!-- 9. Call to Action -->
    <div class="section-card" id="section-cta">
        <div class="card-top" data-target="body-cta">
            <div class="left">
                <span class="num" style="background: #FC4438;">9</span>
                <div>
                    <h6>Call to Action</h6>
                    <small>"Ready to Join" banner near bottom</small>
                </div>
            </div>
            <span class="chevron"><i class="icon-angle-down"></i></span>
        </div>
        <div class="card-body-inner" id="body-cta">
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
    </div>

    <!-- 10. Footer (link) -->
    <div class="link-card">
        <a href="{{ route('admin.settings.school') }}">
            <div class="left">
                <span class="num">10</span>
                <div>
                    <h6>Footer</h6>
                    <small>School name, address, phone, email</small>
                </div>
            </div>
            <span class="go-icon"><i class="icon-arrow-right"></i></span>
        </a>
    </div>

    <!-- Save -->
    <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
        <a href="{{ route('admin.website.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;"><i class="icon-arrow-left me-1"></i> Back</a>
        <button type="submit" class="btn btn-primary px-5" style="border-radius: 8px; font-weight: 600;"><i class="icon-check me-1"></i> Save Changes</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Accordion toggle
    jQuery('.section-card .card-top').on('click', function() {
        var target = jQuery('#' + jQuery(this).data('target'));
        var isOpen = target.hasClass('open');

        // Close all
        jQuery('.card-body-inner').removeClass('open');
        jQuery('.section-card .card-top').removeClass('open');

        // Open clicked
        if (!isOpen) {
            target.addClass('open');
            jQuery(this).addClass('open');
            setTimeout(function() {
                jQuery('html, body').animate({ scrollTop: target.closest('.section-card').offset().top - 80 }, 300);
            }, 50);
        }
    });

    // Auto-open section from URL hash
    var hash = window.location.hash;
    if (hash) {
        var card = jQuery(hash);
        if (card.length) {
            var top = card.find('.card-top');
            if (top.length) {
                top.trigger('click');
            }
        }
    }

    @if(session('success'))
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session("success") }}', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    @endif
});
</script>
@endpush
