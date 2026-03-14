@extends('layouts.app')

@section('title', 'School Settings')

@section('page-title', 'School Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- School Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>School Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_name" class="form-label">School Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="school_name" name="school_name" 
                                       value="{{ old('school_name', $settings['school_name']) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_email" class="form-label">School Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="school_email" name="school_email" 
                                       value="{{ old('school_email', $settings['school_email']) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_phone" class="form-label">School Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="school_phone" name="school_phone" 
                                       value="{{ old('school_phone', $settings['school_phone']) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_website" class="form-label">School Website</label>
                                <input type="text" class="form-control" id="school_website" name="school_website"
                                       value="{{ old('school_website', $settings['school_website']) }}"
                                       placeholder="www.example.com">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="school_address" class="form-label">School Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="school_address" name="school_address" rows="3" required>{{ old('school_address', $settings['school_address']) }}</textarea>
                    </div>

                    <div class="card border mb-3 mt-3">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Contact Page - Google Map</h6>
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="show_map" class="form-check-input" id="show_map" value="1" {{ old('show_map', $settings['show_map'] ?? '') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_map">Show Map on Contact Page</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="mapSettings" style="{{ old('show_map', $settings['show_map'] ?? '') ? '' : 'display: none;' }}">
                            <div class="mb-3">
                                <label for="school_map_embed" class="form-label">Google Map Embed Code</label>
                                <textarea class="form-control" id="school_map_embed" name="school_map_embed" rows="4" placeholder='Paste Google Maps embed iframe code here...'>{{ old('school_map_embed', $settings['school_map_embed'] ?? '') }}</textarea>
                                <small class="text-muted mt-1 d-block"><strong>How to get the code:</strong> Open <strong>Google Maps</strong> → Search your school location → Click <strong>Share</strong> → Click <strong>Embed a map</strong> → Copy the HTML code and paste here.</small>
                            </div>
                            @if($settings['school_map_embed'] ?? '')
                                <div class="mb-0">
                                    <label class="form-label">Preview</label>
                                    <div style="height: 200px; border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                        {!! $settings['school_map_embed'] !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_logo" class="form-label">School Logo</label>
                                <input type="file" class="form-control" id="school_logo" name="school_logo" accept="image/*">
                                @if($settings['school_logo'])
                                    <div class="mt-2 d-flex align-items-start gap-3">
                                        <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="School Logo" class="img-thumbnail" style="max-height: 100px;">
                                        <div>
                                            <p class="small text-muted mb-1">Current logo</p>
                                            <label class="text-danger small" style="cursor: pointer;">
                                                <input type="checkbox" name="remove_logo" value="1" class="me-1"> Remove logo
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <small class="text-muted">Recommended size: 200x100px, Max: 2MB. Used in sidebar and headers.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_favicon" class="form-label">Favicon</label>
                                <input type="file" class="form-control" id="school_favicon" name="school_favicon" accept="image/*,.ico">
                                @if($settings['school_favicon'])
                                    <div class="mt-2 d-flex align-items-start gap-3">
                                        <img src="{{ asset('storage/' . $settings['school_favicon']) }}" alt="Favicon" class="img-thumbnail" style="max-height: 50px;">
                                        <div>
                                            <p class="small text-muted mb-1">Current favicon</p>
                                            <label class="text-danger small" style="cursor: pointer;">
                                                <input type="checkbox" name="remove_favicon" value="1" class="me-1"> Remove favicon
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <small class="text-muted">Recommended: 32x32px or 64x64px PNG/ICO. Shows in browser tab.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Principal Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Principal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="principal_name" class="form-label">Principal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="principal_name" name="principal_name" 
                                       value="{{ old('principal_name', $settings['principal_name']) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="principal_signature" class="form-label">Principal Designation</label>
                                <input type="text" class="form-control" id="principal_signature" name="principal_signature" 
                                       value="{{ old('principal_signature', $settings['principal_signature']) }}" 
                                       placeholder="e.g., Principal, Director">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature Settings -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Signature Settings</h5>
                    <p class="mb-0 small text-muted">Configure how signatures appear on receipts and documents</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="authorized_signature_text" class="form-label">Signature Text</label>
                                <input type="text" class="form-control" id="authorized_signature_text" name="authorized_signature_text" 
                                       value="{{ old('authorized_signature_text', $settings['authorized_signature_text']) }}" 
                                       placeholder="e.g., Authorized Signatory, Accounts Officer">
                                <small class="text-muted">This text will appear below the signature line on receipts</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="signature_image" class="form-label">Signature Image (Optional)</label>
                                <input type="file" class="form-control" id="signature_image" name="signature_image" accept="image/*">
                                @if($settings['signature_image'])
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $settings['signature_image']) }}" alt="Signature" 
                                             class="img-thumbnail" style="max-height: 80px;">
                                        <p class="small text-muted mt-1">Current signature (upload new to replace)</p>
                                    </div>
                                @endif
                                <small class="text-muted">Recommended size: 200x80px, Max: 1MB</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light-primary txt-primary" role="alert">
                        <p class="mb-2"><strong>How signatures work:</strong></p>
                        <ul class="mb-0 ps-3">
                            <li><strong>Text only:</strong> Shows admin name + signature text</li>
                            <li><strong>Image only:</strong> Shows uploaded signature image + admin name</li>
                            <li><strong>Both:</strong> Shows signature image + signature text</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Social Media Links</h5>
                    <p class="mb-0 small text-muted">Displayed on the website header and footer. Leave empty to hide a platform.</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="social_facebook" class="form-label">
                                    <i data-feather="facebook" style="width: 16px; height: 16px;" class="me-1"></i> Facebook
                                </label>
                                <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" placeholder="https://facebook.com/yourpage">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="social_twitter" class="form-label">
                                    <i data-feather="twitter" style="width: 16px; height: 16px;" class="me-1"></i> Twitter / X
                                </label>
                                <input type="url" class="form-control" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" placeholder="https://twitter.com/yourhandle">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="social_instagram" class="form-label">
                                    <i data-feather="instagram" style="width: 16px; height: 16px;" class="me-1"></i> Instagram
                                </label>
                                <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/yourprofile">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="social_youtube" class="form-label">
                                    <i data-feather="youtube" style="width: 16px; height: 16px;" class="me-1"></i> YouTube
                                </label>
                                <input type="url" class="form-control" id="social_youtube" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" placeholder="https://youtube.com/yourchannel">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="social_linkedin" class="form-label">
                                    <i data-feather="linkedin" style="width: 16px; height: 16px;" class="me-1"></i> LinkedIn
                                </label>
                                <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" placeholder="https://linkedin.com/company/yourschool">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="social_whatsapp" class="form-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg> WhatsApp
                                </label>
                                <input type="url" class="form-control" id="social_whatsapp" name="social_whatsapp" value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? '') }}" placeholder="https://wa.me/919876543210">
                                <small class="text-muted">Format: https://wa.me/COUNTRYCODEPHONENUMBER (e.g., https://wa.me/919876543210)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Office Hours -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Office Hours</h5>
                    <p class="mb-0 small text-muted">Displayed on the Contact page of the website</p>
                </div>
                <div class="card-body">
                    @php
                        $timeOptions = [];
                        for ($h = 6; $h <= 22; $h++) {
                            foreach (['00', '30'] as $m) {
                                $ampm = $h < 12 ? 'AM' : 'PM';
                                $hour12 = $h % 12 ?: 12;
                                $timeOptions[] = $hour12 . ':' . $m . ' ' . $ampm;
                            }
                        }

                        $officeHoursRows = [
                            ['label' => 'School Office', 'prefix' => 'school', 'days' => [
                                ['key' => 'mf', 'label' => 'Monday - Friday', 'def_open' => '8:00 AM', 'def_close' => '4:00 PM', 'def_status' => 'open'],
                                ['key' => 'sat', 'label' => 'Saturday', 'def_open' => '9:00 AM', 'def_close' => '1:00 PM', 'def_status' => 'open'],
                                ['key' => 'sun', 'label' => 'Sunday', 'def_open' => '', 'def_close' => '', 'def_status' => 'closed'],
                            ]],
                            ['label' => 'Admissions Office', 'prefix' => 'admission', 'days' => [
                                ['key' => 'mf', 'label' => 'Monday - Friday', 'def_open' => '9:00 AM', 'def_close' => '3:00 PM', 'def_status' => 'open'],
                                ['key' => 'sat', 'label' => 'Saturday', 'def_open' => '10:00 AM', 'def_close' => '12:00 PM', 'def_status' => 'open'],
                                ['key' => 'sun', 'label' => 'Sunday', 'def_open' => '', 'def_close' => '', 'def_status' => 'closed'],
                            ]],
                        ];
                    @endphp

                    <div class="row">
                        @foreach($officeHoursRows as $office)
                            <div class="col-md-6">
                                <h6 class="mb-3">{{ $office['label'] }}</h6>
                                @foreach($office['days'] as $day)
                                    @php
                                        $statusKey = 'office_hours_' . $office['prefix'] . '_' . $day['key'] . '_status';
                                        $openKey = 'office_hours_' . $office['prefix'] . '_' . $day['key'] . '_open';
                                        $closeKey = 'office_hours_' . $office['prefix'] . '_' . $day['key'] . '_close';
                                        $currentStatus = old($statusKey, $settings[$statusKey] ?? $day['def_status']);
                                        $currentOpen = old($openKey, $settings[$openKey] ?? $day['def_open']);
                                        $currentClose = old($closeKey, $settings[$closeKey] ?? $day['def_close']);
                                    @endphp
                                    <div class="mb-3">
                                        <label class="form-label d-flex align-items-center justify-content-between">
                                            <span>{{ $day['label'] }}</span>
                                            <div class="form-check form-switch mb-0">
                                                <input type="hidden" name="{{ $statusKey }}" value="closed">
                                                <input type="checkbox" class="form-check-input office-status-toggle" name="{{ $statusKey }}" value="open" id="{{ $statusKey }}" data-target="{{ $office['prefix'] }}_{{ $day['key'] }}" {{ $currentStatus === 'open' ? 'checked' : '' }}>
                                                <label class="form-check-label small {{ $currentStatus === 'open' ? 'text-success' : 'text-danger' }}" for="{{ $statusKey }}" id="{{ $statusKey }}_label">{{ $currentStatus === 'open' ? 'Open' : 'Closed' }}</label>
                                            </div>
                                        </label>
                                        <div class="row g-2 office-time-row" id="time_{{ $office['prefix'] }}_{{ $day['key'] }}" style="{{ $currentStatus !== 'open' ? 'display:none;' : '' }}">
                                            <div class="col-6">
                                                <select class="form-select form-select-sm" name="{{ $openKey }}">
                                                    @foreach($timeOptions as $t)
                                                        <option value="{{ $t }}" {{ $currentOpen === $t ? 'selected' : '' }}>{{ $t }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <select class="form-select form-select-sm" name="{{ $closeKey }}">
                                                    @foreach($timeOptions as $t)
                                                        <option value="{{ $t }}" {{ $currentClose === $t ? 'selected' : '' }}>{{ $t }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="office-closed-text text-danger small mt-1" id="closed_{{ $office['prefix'] }}_{{ $day['key'] }}" style="{{ $currentStatus === 'open' ? 'display:none;' : '' }}">
                                            <i data-feather="x-circle" style="width: 14px; height: 14px;"></i> Closed
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="text-end mb-4">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save" class="icon-xs"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
	jQuery(document).ready(function() {
		// Preview uploaded images
		jQuery('#school_logo').on('change', function() {
			previewImage(this, 'logo-preview');
		});

		jQuery('#signature_image').on('change', function() {
			previewImage(this, 'signature-preview');
		});

		jQuery('#show_map').on('change', function() {
			if (jQuery(this).is(':checked')) {
				jQuery('#mapSettings').slideDown();
			} else {
				jQuery('#mapSettings').slideUp();
			}
		});

		jQuery('.office-status-toggle').on('change', function() {
			var target = jQuery(this).data('target');
			var label = jQuery('#' + jQuery(this).attr('id') + '_label');
			if (jQuery(this).is(':checked')) {
				jQuery('#time_' + target).slideDown();
				jQuery('#closed_' + target).slideUp();
				label.text('Open').removeClass('text-danger').addClass('text-success');
			} else {
				jQuery('#time_' + target).slideUp();
				jQuery('#closed_' + target).slideDown();
				label.text('Closed').removeClass('text-success').addClass('text-danger');
			}
		});

		function previewImage(input, previewId) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function(e) {
					var preview = jQuery('#' + previewId);
					if (preview.length === 0) {
						jQuery(input).after('<div id="' + previewId + '" class="mt-2"><img class="img-thumbnail" style="max-height: 100px;"><p class="small text-muted mt-1">Preview</p></div>');
						preview = jQuery('#' + previewId);
					}
					preview.find('img').attr('src', e.target.result);
				};
				reader.readAsDataURL(input.files[0]);
			}
		}
	});
</script>
@endpush