@extends('layouts.app')

@section('title', 'Homepage Sections')

@section('page-title', 'Homepage Sections')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item active">Homepage Sections</li>
@endsection

@section('content')
<form action="{{ route('admin.website.update-homepage-sections') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="icon-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <!-- Why Choose Us Section -->
            <div class="card">
                <div class="card-header">
                    <h5><i data-feather="star" class="me-2"></i> "Why Choose Us" Section</h5>
                    <p class="text-muted mb-0 mt-1">Edit the feature cards shown on the homepage</p>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="homepage_why_title" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_title', 'Why Choose Us') }}" placeholder="Why Choose Us">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Subtitle</label>
                            <input type="text" name="homepage_why_subtitle" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_subtitle', 'Discover what makes our school the perfect place for your child\'s education') }}" placeholder="Subtitle text">
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Feature 1 -->
                    <h6 class="text-primary mb-3">Feature Card 1</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Icon</label>
                            <input type="text" name="homepage_why_1_icon" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_1_icon', 'book-open') }}" placeholder="e.g. book-open">
                            <small class="text-muted">Feather icon name (see feathericons.com)</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="homepage_why_1_title" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_1_title', 'Quality Education') }}" placeholder="Feature title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" name="homepage_why_1_desc" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_1_desc', 'Comprehensive curriculum designed to nurture young minds and develop critical thinking skills.') }}" placeholder="Feature description">
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <h6 class="text-primary mb-3">Feature Card 2</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Icon</label>
                            <input type="text" name="homepage_why_2_icon" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_2_icon', 'users') }}" placeholder="e.g. users">
                            <small class="text-muted">Feather icon name (see feathericons.com)</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="homepage_why_2_title" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_2_title', 'Expert Faculty') }}" placeholder="Feature title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" name="homepage_why_2_desc" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_2_desc', 'Dedicated teachers with years of experience in education and child development.') }}" placeholder="Feature description">
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <h6 class="text-primary mb-3">Feature Card 3</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Icon</label>
                            <input type="text" name="homepage_why_3_icon" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_3_icon', 'award') }}" placeholder="e.g. award">
                            <small class="text-muted">Feather icon name (see feathericons.com)</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="homepage_why_3_title" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_3_title', 'Modern Facilities') }}" placeholder="Feature title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" name="homepage_why_3_desc" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_3_desc', 'State-of-the-art classrooms, labs, and recreational areas for holistic development.') }}" placeholder="Feature description">
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <h6 class="text-primary mb-3">Feature Card 4</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Icon</label>
                            <input type="text" name="homepage_why_4_icon" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_4_icon', 'heart') }}" placeholder="e.g. heart">
                            <small class="text-muted">Feather icon name (see feathericons.com)</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="homepage_why_4_title" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_4_title', 'Safe Environment') }}" placeholder="Feature title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" name="homepage_why_4_desc" class="form-control" value="{{ \App\Models\Setting::get('homepage_why_4_desc', 'Secure campus with caring staff ensuring your child\'s safety and well-being.') }}" placeholder="Feature description">
                        </div>
                    </div>
                </div>
            </div>

            <!-- About Us Section -->
            <div class="card">
                <div class="card-header">
                    <h5><i data-feather="info" class="me-2"></i> "About Us" Section</h5>
                    <p class="text-muted mb-0 mt-1">Edit the about section displayed on the homepage</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Image</label>
                            <input type="file" name="homepage_about_image" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended size: 600x450px. Leave empty to keep current image.</small>
                            @php $aboutImage = \App\Models\Setting::get('homepage_about_image'); @endphp
                            @if($aboutImage)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $aboutImage) }}" alt="About Image" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="homepage_about_subtitle" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_subtitle', 'ABOUT US') }}" placeholder="e.g. ABOUT US">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="homepage_about_title" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_title', 'A Legacy of Educational Excellence') }}" placeholder="Section title">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="homepage_about_description" class="form-control" rows="4" placeholder="About us description text">{{ \App\Models\Setting::get('homepage_about_description', 'Our school has been a beacon of educational excellence for over two decades. We are committed to providing quality education that shapes young minds and prepares them for the challenges of tomorrow.') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="text-primary mb-3">Checklist Items</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Checklist Item 1</label>
                            <input type="text" name="homepage_about_check_1" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_check_1', 'Experienced Teachers') }}" placeholder="e.g. Experienced Teachers">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Checklist Item 2</label>
                            <input type="text" name="homepage_about_check_2" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_check_2', 'Modern Curriculum') }}" placeholder="e.g. Modern Curriculum">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Checklist Item 3</label>
                            <input type="text" name="homepage_about_check_3" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_check_3', 'Character Building') }}" placeholder="e.g. Character Building">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Checklist Item 4</label>
                            <input type="text" name="homepage_about_check_4" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_check_4', 'Sports Activities') }}" placeholder="e.g. Sports Activities">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Checklist Item 5</label>
                            <input type="text" name="homepage_about_check_5" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_check_5', 'Smart Classes') }}" placeholder="e.g. Smart Classes">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Checklist Item 6</label>
                            <input type="text" name="homepage_about_check_6" class="form-control" value="{{ \App\Models\Setting::get('homepage_about_check_6', 'Safe Environment') }}" placeholder="e.g. Safe Environment">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="card">
                <div class="card-header">
                    <h5><i data-feather="bar-chart-2" class="me-2"></i> "Statistics" Section</h5>
                    <p class="text-muted mb-0 mt-1">Edit the statistics counters displayed on the homepage</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Stat 1 - Number</label>
                            <input type="text" name="total_students" class="form-control" value="{{ \App\Models\Setting::get('total_students', '1500') }}" placeholder="e.g. 1500">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stat 1 - Label</label>
                            <input type="text" name="stat_1_label" class="form-control" value="{{ \App\Models\Setting::get('stat_1_label', 'Students') }}" placeholder="e.g. Students">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stat 2 - Number</label>
                            <input type="text" name="total_teachers" class="form-control" value="{{ \App\Models\Setting::get('total_teachers', '100') }}" placeholder="e.g. 100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stat 2 - Label</label>
                            <input type="text" name="stat_2_label" class="form-control" value="{{ \App\Models\Setting::get('stat_2_label', 'Teachers') }}" placeholder="e.g. Teachers">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stat 3 - Number</label>
                            <input type="text" name="school_years" class="form-control" value="{{ \App\Models\Setting::get('school_years', '25') }}" placeholder="e.g. 25">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stat 3 - Label</label>
                            <input type="text" name="stat_3_label" class="form-control" value="{{ \App\Models\Setting::get('stat_3_label', 'Years') }}" placeholder="e.g. Years">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stat 4 - Number</label>
                            <input type="text" name="awards_count" class="form-control" value="{{ \App\Models\Setting::get('awards_count', '50') }}" placeholder="e.g. 50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stat 4 - Label</label>
                            <input type="text" name="stat_4_label" class="form-control" value="{{ \App\Models\Setting::get('stat_4_label', 'Awards') }}" placeholder="e.g. Awards">
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section ("Ready to Join") -->
            <div class="card">
                <div class="card-header">
                    <h5>"Ready to Join" Section</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Heading</label>
                            <input type="text" name="cta_heading" class="form-control" value="{{ \App\Models\Setting::get('cta_heading', 'Ready to Join Our School?') }}" placeholder="e.g. Ready to Join Our School?">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="cta_subtitle" class="form-control" value="{{ \App\Models\Setting::get('cta_subtitle', 'Take the first step towards your child\'s bright future') }}" placeholder="e.g. Take the first step...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Button Text</label>
                            <input type="text" name="cta_button_text" class="form-control" value="{{ \App\Models\Setting::get('cta_button_text', 'Contact Us Today') }}" placeholder="e.g. Contact Us Today">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Button Link</label>
                            <input type="text" name="cta_button_link" class="form-control" value="{{ \App\Models\Setting::get('cta_button_link', '') }}" placeholder="Leave empty for Contact page">
                            <small class="text-muted">Leave empty to link to Contact Us page</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Background Image</label>
                            <input type="file" name="cta_bg_image" class="form-control" accept="image/*">
                            @php $ctaBg = \App\Models\Setting::get('cta_bg_image'); @endphp
                            @if($ctaBg)
                                <small class="text-success">Current: <a href="{{ asset('storage/' . $ctaBg) }}" target="_blank">View</a></small>
                            @else
                                <small class="text-muted">Optional. 1920 x 400px recommended</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.website.index') }}" class="btn btn-light">
                            <i data-feather="arrow-left" class="me-1"></i> Back to Website Management
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') feather.replace();

    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endif
});
</script>
@endpush
