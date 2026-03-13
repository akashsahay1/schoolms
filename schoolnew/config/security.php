<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    |
    | Configure password requirements for user accounts.
    |
    */

    'password' => [
        'min_length' => env('PASSWORD_MIN_LENGTH', 8),
        'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
        'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
        'require_special' => env('PASSWORD_REQUIRE_SPECIAL', false),
        'max_age_days' => env('PASSWORD_MAX_AGE_DAYS', 90), // 0 = never expires
        'history_count' => env('PASSWORD_HISTORY_COUNT', 3), // Prevent reusing last N passwords
    ],

    /*
    |--------------------------------------------------------------------------
    | Login Security
    |--------------------------------------------------------------------------
    |
    | Configure login-related security settings.
    |
    */

    'login' => [
        'max_attempts' => env('LOGIN_MAX_ATTEMPTS', 5),
        'lockout_minutes' => env('LOGIN_LOCKOUT_MINUTES', 15),
        'remember_me_days' => env('REMEMBER_ME_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    |
    | Configure session-related security settings.
    |
    */

    'session' => [
        'regenerate_on_login' => true,
        'destroy_on_logout' => true,
        'single_device' => env('SESSION_SINGLE_DEVICE', false), // Only allow one active session
        'idle_timeout' => env('SESSION_IDLE_TIMEOUT', 30), // Minutes of inactivity before logout
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    |
    | Configure file upload restrictions.
    |
    */

    'uploads' => [
        'max_size' => env('UPLOAD_MAX_SIZE', 10240), // KB
        'allowed_extensions' => [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'],
        ],
        'disallowed_extensions' => [
            'php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'phar',
            'exe', 'bat', 'cmd', 'sh', 'bash', 'ps1',
            'htaccess', 'htpasswd',
        ],
        'scan_for_malware' => env('UPLOAD_SCAN_MALWARE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Security
    |--------------------------------------------------------------------------
    |
    | Configure API-related security settings.
    |
    */

    'api' => [
        'rate_limit_per_minute' => env('API_RATE_LIMIT', 60),
        'require_authentication' => true,
        'log_requests' => env('API_LOG_REQUESTS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Security
    |--------------------------------------------------------------------------
    |
    | Configure admin-related security settings.
    |
    */

    'admin' => [
        'ip_whitelist' => array_filter(explode(',', env('ADMIN_IP_WHITELIST', ''))),
        'require_2fa' => env('ADMIN_REQUIRE_2FA', false),
        'activity_logging' => env('ADMIN_ACTIVITY_LOGGING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure HTTP security headers.
    |
    */

    'headers' => [
        'hsts' => [
            'enabled' => env('SECURITY_HSTS_ENABLED', true),
            'max_age' => env('SECURITY_HSTS_MAX_AGE', 31536000),
            'include_subdomains' => env('SECURITY_HSTS_SUBDOMAINS', true),
            'preload' => env('SECURITY_HSTS_PRELOAD', false),
        ],
        'content_type_nosniff' => true,
        'x_frame_options' => 'SAMEORIGIN',
        'x_xss_protection' => true,
        'referrer_policy' => 'strict-origin-when-cross-origin',
    ],

];
