@extends('layouts.app')

@section('title', 'Payment Gateway Settings')

@section('page-title', 'Payment Gateway Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
    <li class="breadcrumb-item active">Payment Gateway</li>
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

        <form action="{{ route('admin.settings.payment.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Gateway Selection -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Payment Gateway Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gateway" class="form-label">Select Payment Gateway <span class="text-danger">*</span></label>
                                <select class="form-select" id="gateway" name="gateway" required>
                                    @foreach($gateways as $key => $name)
                                        <option value="{{ $key }}" {{ ($setting->gateway ?? 'razorpay') == $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                <select class="form-select" id="currency" name="currency" required>
                                    <option value="INR" {{ ($setting->currency ?? 'INR') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                    <option value="USD" {{ ($setting->currency ?? '') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ ($setting->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ ($setting->currency ?? '') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ ($setting->is_active ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Enable Online Payments</strong>
                                    <br><small class="text-muted">Allow students to pay fees online</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_demo_mode" name="is_demo_mode" {{ ($setting->is_demo_mode ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_demo_mode">
                                    <strong>Demo Mode</strong>
                                    <br><small class="text-muted">Simulate payments without actual transactions</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Credentials -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>API Credentials</h5>
                    <small class="text-muted">Enter your payment gateway API credentials. Leave blank to keep existing values.</small>
                </div>
                <div class="card-body">
                    <!-- Razorpay / Stripe Fields -->
                    <div id="razorpay-fields" class="gateway-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="key_id" class="form-label">
                                        <span class="key-label">API Key ID</span>
                                        <small class="text-muted">(Razorpay Key ID / Stripe Publishable Key)</small>
                                    </label>
                                    <input type="text" class="form-control" id="key_id" name="key_id"
                                           placeholder="{{ $setting && $setting->key_id ? '••••••••' . substr($setting->key_id, -4) : 'Enter API Key ID' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="key_secret" class="form-label">
                                        <span class="secret-label">API Key Secret</span>
                                        <small class="text-muted">(Razorpay Key Secret / Stripe Secret Key)</small>
                                    </label>
                                    <input type="password" class="form-control" id="key_secret" name="key_secret"
                                           placeholder="{{ $setting && $setting->key_secret ? '••••••••••••' : 'Enter API Key Secret' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PayU / Paytm Fields -->
                    <div id="payu-fields" class="gateway-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="merchant_id" class="form-label">Merchant ID / MID</label>
                                    <input type="text" class="form-control" id="merchant_id" name="merchant_id"
                                           placeholder="{{ $setting && $setting->merchant_id ? '••••••••' . substr($setting->merchant_id, -4) : 'Enter Merchant ID' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="salt" class="form-label">Salt / Merchant Key</label>
                                    <input type="password" class="form-control" id="salt" name="salt"
                                           placeholder="{{ $setting && $setting->salt ? '••••••••••••' : 'Enter Salt / Key' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Webhook Secret -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="webhook_secret" class="form-label">
                                    Webhook Secret <small class="text-muted">(Optional)</small>
                                </label>
                                <input type="password" class="form-control" id="webhook_secret" name="webhook_secret"
                                       placeholder="{{ $setting && $setting->webhook_secret ? '••••••••••••' : 'Enter Webhook Secret' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gateway Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Gateway Information</h5>
                </div>
                <div class="card-body">
                    <div id="gateway-info">
                        <div class="gateway-info-content" data-gateway="razorpay">
                            <h6><i class="fa fa-info-circle text-primary me-2"></i>Razorpay Setup</h6>
                            <ol class="mb-0">
                                <li>Go to <a href="https://dashboard.razorpay.com" target="_blank">Razorpay Dashboard</a></li>
                                <li>Navigate to Settings > API Keys</li>
                                <li>Generate a new key pair (Test or Live)</li>
                                <li>Copy the Key ID and Key Secret</li>
                            </ol>
                        </div>
                        <div class="gateway-info-content" data-gateway="stripe" style="display: none;">
                            <h6><i class="fa fa-info-circle text-primary me-2"></i>Stripe Setup</h6>
                            <ol class="mb-0">
                                <li>Go to <a href="https://dashboard.stripe.com/apikeys" target="_blank">Stripe Dashboard</a></li>
                                <li>Copy the Publishable key as "API Key ID"</li>
                                <li>Copy the Secret key as "API Key Secret"</li>
                                <li>For webhooks, use the Signing secret</li>
                            </ol>
                        </div>
                        <div class="gateway-info-content" data-gateway="payu" style="display: none;">
                            <h6><i class="fa fa-info-circle text-primary me-2"></i>PayU Setup</h6>
                            <ol class="mb-0">
                                <li>Go to <a href="https://payu.in/dashboard" target="_blank">PayU Dashboard</a></li>
                                <li>Navigate to Payment Gateway > My Account > Merchant Key-Salt</li>
                                <li>Copy the Merchant Key and Salt</li>
                                <li>Enter API Key ID as Merchant Key, Salt as Salt</li>
                            </ol>
                        </div>
                        <div class="gateway-info-content" data-gateway="paytm" style="display: none;">
                            <h6><i class="fa fa-info-circle text-primary me-2"></i>Paytm Setup</h6>
                            <ol class="mb-0">
                                <li>Go to <a href="https://dashboard.paytm.com" target="_blank">Paytm Dashboard</a></li>
                                <li>Navigate to Developer Settings</li>
                                <li>Copy MID (Merchant ID) and Merchant Key</li>
                                <li>Enter MID in Merchant ID field, Key in Salt field</li>
                            </ol>
                        </div>
                        <div class="gateway-info-content" data-gateway="phonepe" style="display: none;">
                            <h6><i class="fa fa-info-circle text-primary me-2"></i>PhonePe Setup</h6>
                            <ol class="mb-0">
                                <li>Contact PhonePe for merchant credentials</li>
                                <li>You will receive Merchant ID and Salt Key</li>
                                <li>Enter Merchant ID and Salt in respective fields</li>
                            </ol>
                        </div>
                        <div class="gateway-info-content" data-gateway="cashfree" style="display: none;">
                            <h6><i class="fa fa-info-circle text-primary me-2"></i>Cashfree Setup</h6>
                            <ol class="mb-0">
                                <li>Go to <a href="https://merchant.cashfree.com" target="_blank">Cashfree Dashboard</a></li>
                                <li>Navigate to Developers > API Keys</li>
                                <li>Copy App ID as "API Key ID"</li>
                                <li>Copy Secret Key as "API Key Secret"</li>
                            </ol>
                        </div>
                        <div class="gateway-info-content" data-gateway="instamojo" style="display: none;">
                            <h6><i class="fa fa-info-circle text-primary me-2"></i>Instamojo Setup</h6>
                            <ol class="mb-0">
                                <li>Go to <a href="https://www.instamojo.com/dashboard" target="_blank">Instamojo Dashboard</a></li>
                                <li>Navigate to API & Plugins > Generate Credentials</li>
                                <li>Copy API Key as "API Key ID"</li>
                                <li>Copy Auth Token as "API Key Secret"</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-info" id="testConnection">
                            <i class="fa fa-plug me-2"></i>Test Connection
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>Save Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Gateway change handler
    function updateGatewayFields() {
        var gateway = jQuery('#gateway').val();

        // Show/hide appropriate fields
        if (['payu', 'paytm', 'phonepe'].includes(gateway)) {
            jQuery('#payu-fields').show();
        } else {
            jQuery('#payu-fields').hide();
        }

        // Update gateway info
        jQuery('.gateway-info-content').hide();
        jQuery('.gateway-info-content[data-gateway="' + gateway + '"]').show();

        // Update labels based on gateway
        switch(gateway) {
            case 'stripe':
                jQuery('.key-label').text('Publishable Key');
                jQuery('.secret-label').text('Secret Key');
                break;
            case 'razorpay':
                jQuery('.key-label').text('Key ID');
                jQuery('.secret-label').text('Key Secret');
                break;
            case 'cashfree':
                jQuery('.key-label').text('App ID');
                jQuery('.secret-label').text('Secret Key');
                break;
            case 'instamojo':
                jQuery('.key-label').text('API Key');
                jQuery('.secret-label').text('Auth Token');
                break;
            default:
                jQuery('.key-label').text('API Key ID');
                jQuery('.secret-label').text('API Key Secret');
        }
    }

    jQuery('#gateway').on('change', updateGatewayFields);
    updateGatewayFields(); // Initial call

    // Test connection
    jQuery('#testConnection').on('click', function() {
        var btn = jQuery(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Testing...');

        jQuery.ajax({
            url: '{{ route("admin.settings.payment.test") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    icon: response.success ? 'success' : 'error',
                    title: response.success ? 'Success' : 'Error',
                    text: response.message
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to test connection. Please try again.'
                });
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-plug me-2"></i>Test Connection');
            }
        });
    });
});
</script>
@endpush
