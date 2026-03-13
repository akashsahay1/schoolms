<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SecurityService
{
    /**
     * Validate password against policy
     */
    public static function validatePassword(string $password): array
    {
        $errors = [];
        $config = config('security.password');

        // Minimum length
        if (strlen($password) < $config['min_length']) {
            $errors[] = "Password must be at least {$config['min_length']} characters long.";
        }

        // Uppercase requirement
        if ($config['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }

        // Lowercase requirement
        if ($config['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }

        // Number requirement
        if ($config['require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }

        // Special character requirement
        if ($config['require_special'] && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }

        return $errors;
    }

    /**
     * Check if password is in history (previously used)
     */
    public static function isPasswordInHistory(string $password, array $hashedHistory): bool
    {
        foreach ($hashedHistory as $hashedPassword) {
            if (Hash::check($password, $hashedPassword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sanitize input to prevent XSS
     */
    public static function sanitizeInput(string $input): string
    {
        // Remove HTML tags
        $input = strip_tags($input);

        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $input;
    }

    /**
     * Sanitize array of inputs
     */
    public static function sanitizeArray(array $inputs, array $except = []): array
    {
        $sanitized = [];

        foreach ($inputs as $key => $value) {
            if (in_array($key, $except)) {
                $sanitized[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value, $except);
            } elseif (is_string($value)) {
                $sanitized[$key] = self::sanitizeInput($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, string $type = 'documents'): array
    {
        $errors = [];
        $config = config('security.uploads');

        if (!$file) {
            return ['No file provided.'];
        }

        // Check file size
        $maxSize = $config['max_size'] * 1024; // Convert to bytes
        if ($file->getSize() > $maxSize) {
            $errors[] = "File size exceeds the maximum allowed size of " . ($config['max_size'] / 1024) . " MB.";
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());

        // Check against disallowed extensions
        if (in_array($extension, $config['disallowed_extensions'])) {
            $errors[] = "File type '{$extension}' is not allowed for security reasons.";
        }

        // Check against allowed extensions for the type
        if (isset($config['allowed_extensions'][$type])) {
            if (!in_array($extension, $config['allowed_extensions'][$type])) {
                $allowed = implode(', ', $config['allowed_extensions'][$type]);
                $errors[] = "File type '{$extension}' is not allowed. Allowed types: {$allowed}";
            }
        }

        // Check MIME type matches extension
        $mimeType = $file->getMimeType();
        $expectedMimes = self::getExpectedMimeTypes($extension);
        if (!empty($expectedMimes) && !in_array($mimeType, $expectedMimes)) {
            $errors[] = 'File content does not match its extension.';
        }

        return $errors;
    }

    /**
     * Get expected MIME types for an extension
     */
    private static function getExpectedMimeTypes(string $extension): array
    {
        $mimeMap = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'csv' => ['text/csv', 'text/plain', 'application/csv'],
            'txt' => ['text/plain'],
        ];

        return $mimeMap[$extension] ?? [];
    }

    /**
     * Log security event
     */
    public static function logSecurityEvent(string $event, array $context = [], string $level = 'warning'): void
    {
        $context['ip'] = request()->ip();
        $context['user_agent'] = request()->userAgent();
        $context['user_id'] = auth()->id();
        $context['timestamp'] = now()->toIso8601String();

        Log::channel('security')->{$level}($event, $context);
    }

    /**
     * Check if IP is in whitelist
     */
    public static function isIpWhitelisted(string $ip): bool
    {
        $whitelist = config('security.admin.ip_whitelist', []);

        if (empty($whitelist)) {
            return true; // No whitelist configured, allow all
        }

        foreach ($whitelist as $whitelistedIp) {
            if (self::ipMatches($ip, trim($whitelistedIp))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP matches (supports CIDR notation)
     */
    private static function ipMatches(string $ip, string $pattern): bool
    {
        // Direct match
        if ($ip === $pattern) {
            return true;
        }

        // CIDR match
        if (strpos($pattern, '/') !== false) {
            list($subnet, $bits) = explode('/', $pattern);
            $subnet = ip2long($subnet);
            $ip = ip2long($ip);
            $mask = -1 << (32 - (int) $bits);

            return ($ip & $mask) === ($subnet & $mask);
        }

        return false;
    }

    /**
     * Generate secure random token
     */
    public static function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Mask sensitive data for logging
     */
    public static function maskSensitiveData(array $data, array $sensitiveKeys = []): array
    {
        $defaultSensitive = ['password', 'password_confirmation', 'current_password', 'credit_card', 'cvv', 'ssn'];
        $sensitiveKeys = array_merge($defaultSensitive, $sensitiveKeys);

        $masked = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $masked[$key] = '***MASKED***';
            } elseif (is_array($value)) {
                $masked[$key] = self::maskSensitiveData($value, $sensitiveKeys);
            } else {
                $masked[$key] = $value;
            }
        }

        return $masked;
    }
}
