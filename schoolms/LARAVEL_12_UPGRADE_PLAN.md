# InfixEdu School Management System
# Laravel 12 & PHP 8.4 Upgrade Plan

**Document Version:** 1.0
**Created:** November 22, 2025
**Current Stack:** Laravel 8.12+ | PHP 7.3/8.0
**Target Stack:** Laravel 12 | PHP 8.4

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Current System Analysis](#current-system-analysis)
3. [Migration Strategy](#migration-strategy)
4. [Phase 1: Environment Preparation](#phase-1-environment-preparation)
5. [Phase 2: Laravel Version Upgrades](#phase-2-laravel-version-upgrades)
6. [Phase 3: Package Dependency Updates](#phase-3-package-dependency-updates)
7. [Phase 4: Payment Gateway Integration](#phase-4-payment-gateway-integration)
8. [Phase 5: Code Modernization](#phase-5-code-modernization)
9. [Phase 6: Module Updates](#phase-6-module-updates)
10. [Phase 7: Frontend Updates](#phase-7-frontend-updates)
11. [Phase 8: Testing & Validation](#phase-8-testing--validation)
12. [Risk Assessment](#risk-assessment)
13. [Rollback Strategy](#rollback-strategy)

---

## Executive Summary

### Migration Overview

| Aspect | Current | Target |
|--------|---------|--------|
| Laravel Version | 8.12+ | 12.x |
| PHP Version | 7.3/8.0 | 8.4 |
| Payment Gateways | 5 | 7 (+PhonePe, +PayU) |

### Codebase Statistics

| Component | Count |
|-----------|-------|
| PHP Files | 638 |
| Controllers | 233 |
| Models | 207 |
| Migrations | 217 |
| Blade Templates | 602 |
| Modules | 12 |
| Route Files | 15 |
| API Endpoints | 1160+ |
| Config Files | 45 |

### Migration Complexity: **MEDIUM-HIGH**

**Estimated Effort:** Significant refactoring required due to the large codebase size and multiple integration points.

### Recommended Approach: **Incremental In-Place Migration**

We recommend migrating in-place rather than creating a new Laravel 12 project because:
- Already on Laravel 8 (close to modern standards)
- Complex module system (nwidart/laravel-modules) would be difficult to replicate
- 217 migrations need to remain intact
- Multi-tenant architecture is deeply integrated
- Existing payment gateway implementations should be preserved

---

## Current System Analysis

### Framework & Architecture

```
Framework:          Laravel 8.12+
Architecture:       Modular MVC with Multi-Tenancy
ORM:               Eloquent
Authentication:     Session-based + Laravel Passport (OAuth2)
API:               RESTful (1160+ endpoints)
Real-time:         Pusher + Laravel Echo + WebSockets
Multi-tenancy:     School-based with subdomain routing
```

### Directory Structure

```
/School/
├── app/
│   ├── Console/              # Artisan commands
│   ├── Events/               # Event classes
│   ├── Exceptions/           # Exception handlers
│   ├── Exports/              # Excel exports (Maatwebsite)
│   ├── Helpers/              # Helper functions
│   ├── Http/
│   │   ├── Controllers/      # 233 controllers
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form requests
│   ├── Jobs/                 # Queue jobs
│   ├── Listeners/            # Event listeners
│   ├── Mail/                 # Mailable classes
│   ├── Models/               # Eloquent models
│   ├── Notifications/        # Notification classes
│   ├── PaymentGateway/       # Payment implementations
│   ├── Providers/            # Service providers
│   ├── Rules/                # Validation rules
│   ├── Scopes/               # Query scopes
│   ├── Traits/               # Reusable traits
│   └── Validators/           # Custom validators
├── Modules/                  # 12 modular extensions
├── config/                   # 45 configuration files
├── database/
│   ├── migrations/           # 217 migration files
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/                # 602 Blade templates
│   ├── js/                   # Vue.js components
│   ├── sass/                 # SCSS stylesheets
│   └── lang/                 # Translations (en, bn, es, fr)
├── routes/                   # 15 route files
├── public/                   # Public assets
├── storage/                  # Storage directories
└── tests/                    # PHPUnit tests
```

### Existing Payment Gateways

| Gateway | File | Status |
|---------|------|--------|
| RazorPay | `app/PaymentGateway/RazorPayPayment.php` | Exists - Update |
| Stripe | `app/PaymentGateway/StripePayment.php` | Exists - Update |
| PayPal | `app/PaymentGateway/PaypalPayment.php` | Exists - Update |
| Paystack | `app/PaymentGateway/PaystackPayment.php` | Exists - Update |
| Xendit | `app/PaymentGateway/XenditPayment.php` | Exists - Update |
| PhonePe | N/A | **NEW - Add** |
| PayU | N/A | **NEW - Add** |

### Current Routes Structure

| File | Purpose | Size |
|------|---------|------|
| `api.php` | REST API endpoints | 1160 lines |
| `web.php` | Web application routes | Main routes |
| `admin.php` | Admin panel routes | Admin ops |
| `admin_tenant.php` | Multi-tenant admin | 171KB |
| `tenant.php` | Tenant-specific routes | Per-tenant |
| `parent.php` | Parent portal | Parent features |
| `student.php` | Student portal | Student features |
| `channels.php` | Broadcasting channels | WebSocket |

---

## Migration Strategy

### Why Incremental Migration?

Laravel provides official upgrade guides for each major version. The safest approach is:

```
Laravel 8 → Laravel 9 → Laravel 10 → Laravel 11 → Laravel 12
```

Each upgrade step is well-documented and allows for:
- Incremental testing at each version
- Easier debugging when issues arise
- Clear rollback points
- Manageable change sets

### Pre-Migration Checklist

- [ ] Complete database backup
- [ ] Complete file system backup
- [ ] Document current `.env` configuration
- [ ] Ensure Git repository is clean
- [ ] Create migration branch
- [ ] Set up staging environment for testing

---

## Phase 1: Environment Preparation

### 1.1 Laravel Herd PHP Configuration

Update Laravel Herd to use PHP 8.4:

```bash
# In Laravel Herd preferences:
# 1. Go to PHP tab
# 2. Select PHP 8.4
# 3. Apply to the School project
```

### 1.2 Verify PHP 8.4 Installation

```bash
php -v
# Expected: PHP 8.4.x

php -m
# Verify required extensions: pdo, mbstring, openssl, tokenizer, xml, ctype, json, bcmath
```

### 1.3 Update Composer

```bash
composer self-update
```

### 1.4 Create Migration Branch

```bash
cd /Volumes/Projects/Projects/School
git checkout -b feature/laravel-12-upgrade
```

### 1.5 Backup Current State

```bash
# Database backup
mysqldump -u root -p school_database > backup_before_upgrade.sql

# Or using Laravel
php artisan backup:run  # If spatie/laravel-backup is installed
```

---

## Phase 2: Laravel Version Upgrades

### 2.1 Laravel 8 → Laravel 9

#### 2.1.1 Update composer.json

```json
{
    "require": {
        "php": "^8.0",
        "laravel/framework": "^9.0",
        "laravel/passport": "^11.0",
        "nwidart/laravel-modules": "^9.0"
    }
}
```

#### 2.1.2 Remove Deprecated Packages

```bash
composer remove fideloper/proxy
composer remove fruitcake/laravel-cors
```

#### 2.1.3 Update TrustedProxy Middleware

Replace `app/Http/Middleware/TrustProxies.php`:

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    protected $proxies;

    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
```

#### 2.1.4 Update Exception Handler

Update `app/Exceptions/Handler.php`:

```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
```

#### 2.1.5 Update Routes

If using `Route::resource` with explicit controller actions, update to invokable syntax or ensure proper namespace handling.

#### 2.1.6 Run Upgrade

```bash
composer update
php artisan optimize:clear
php artisan migrate
```

---

### 2.2 Laravel 9 → Laravel 10

#### 2.2.1 Update composer.json

```json
{
    "require": {
        "php": "^8.1",
        "laravel/framework": "^10.0",
        "laravel/passport": "^11.0"
    }
}
```

#### 2.2.2 Update Model Casts

Convert all models from `$casts` property to `casts()` method:

**Before (Laravel 8/9):**
```php
class User extends Authenticatable
{
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
```

**After (Laravel 10+):**
```php
class User extends Authenticatable
{
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
```

#### 2.2.3 Update Service Provider Registration

Ensure all service providers are properly registered in `config/app.php`.

#### 2.2.4 Run Upgrade

```bash
composer update
php artisan optimize:clear
```

---

### 2.3 Laravel 10 → Laravel 11

#### 2.3.1 Update composer.json

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0"
    }
}
```

#### 2.3.2 Streamlined Application Structure (Optional)

Laravel 11 introduces a simplified structure. You can keep the existing structure or migrate:

**Keep existing structure** - Recommended for this codebase due to:
- 233 controllers
- Complex module system
- Established patterns

#### 2.3.3 Update bootstrap/app.php

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Custom middleware configuration
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception handling
    })->create();
```

#### 2.3.4 Run Upgrade

```bash
composer update
php artisan optimize:clear
```

---

### 2.4 Laravel 11 → Laravel 12

#### 2.4.1 Update composer.json

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0"
    }
}
```

#### 2.4.2 Review Breaking Changes

Check Laravel 12 upgrade guide for any breaking changes (to be released).

#### 2.4.3 Run Final Upgrade

```bash
composer update
php artisan optimize:clear
php artisan migrate
```

---

## Phase 3: Package Dependency Updates

### 3.1 Critical Package Updates

| Package | Current | Target | Breaking Changes |
|---------|---------|--------|------------------|
| `laravel/passport` | 10.1.0 | 12.x | Token handling updates |
| `nwidart/laravel-modules` | 8.2 | 11.x | Module structure updates |
| `maatwebsite/excel` | 3.1 | 3.1.55+ | Minor updates |
| `barryvdh/laravel-dompdf` | 0.8.6 | 2.x | Namespace changes |
| `intervention/image` | 2.5 | 3.x | Complete API rewrite |
| `yajra/laravel-datatables-oracle` | 9.15 | 11.x | Query builder updates |

### 3.2 Packages to Remove

```bash
# Remove deprecated packages
composer remove symfony/polyfill-php70
composer remove symfony/polyfill-php72
composer remove symfony/polyfill-php73
composer remove symfony/polyfill-php80
composer remove symfony/polyfill-php81
composer remove laravel-collective/html  # Use Blade components instead
```

### 3.3 Package-Specific Updates

#### 3.3.1 Laravel Passport Update

```bash
composer require laravel/passport:^12.0
php artisan passport:install --force
```

Update `config/auth.php`:
```php
'guards' => [
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

#### 3.3.2 Intervention Image Update (v2 → v3)

**Before (v2):**
```php
use Intervention\Image\Facades\Image;

$image = Image::make($file)->resize(300, 200);
$image->save($path);
```

**After (v3):**
```php
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$manager = new ImageManager(new Driver());
$image = $manager->read($file);
$image->resize(300, 200);
$image->save($path);
```

#### 3.3.3 Laravel DomPDF Update

```bash
composer require barryvdh/laravel-dompdf:^2.0
```

Update usage:
```php
use Barryvdh\DomPDF\Facade\Pdf;

$pdf = Pdf::loadView('invoice', $data);
return $pdf->download('invoice.pdf');
```

#### 3.3.4 Laravel Modules Update

```bash
composer require nwidart/laravel-modules:^11.0
```

Update each module's `module.json` and service providers.

### 3.4 Updated composer.json (Final)

```json
{
    "name": "infixedu/school-management",
    "type": "project",
    "require": {
        "php": "^8.4",
        "laravel/framework": "^12.0",
        "laravel/passport": "^12.0",
        "laravel/tinker": "^2.9",
        "nwidart/laravel-modules": "^11.0",

        "maatwebsite/excel": "^3.1.55",
        "barryvdh/laravel-dompdf": "^2.0",
        "intervention/image": "^3.0",
        "yajra/laravel-datatables-oracle": "^11.0",

        "guzzlehttp/guzzle": "^7.8",
        "pusher/pusher-php-server": "^7.2",

        "stripe/stripe-php": "^13.0",
        "razorpay/razorpay": "^2.9",
        "phonepe/phonepe-pg-php-sdk": "^1.0",
        "payu/payu-checkout-php-sdk": "^1.0",
        "unicodeveloper/laravel-paystack": "^2.0",
        "xendit/xendit-php": "^4.0",

        "twilio/sdk": "^7.0",
        "benwilkins/laravel-fcm-notification": "^5.0",

        "spatie/valuestore": "^1.3",
        "brian2694/laravel-toastr": "^6.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^11.0",
        "laravel/dusk": "^8.0",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.0"
    }
}
```

---

## Phase 4: Payment Gateway Integration

### 4.1 Payment Gateway Architecture

Create a unified payment gateway interface:

#### 4.1.1 Interface Definition

```php
<?php
// app/PaymentGateway/Contracts/PaymentGatewayInterface.php

namespace App\PaymentGateway\Contracts;

interface PaymentGatewayInterface
{
    public function initialize(array $config): self;

    public function createOrder(array $orderData): array;

    public function verifyPayment(string $paymentId): array;

    public function refund(string $paymentId, float $amount): array;

    public function getWebhookPayload(): array;

    public function handleWebhook(array $payload): bool;
}
```

#### 4.1.2 Base Payment Class

```php
<?php
// app/PaymentGateway/BasePaymentGateway.php

namespace App\PaymentGateway;

use App\PaymentGateway\Contracts\PaymentGatewayInterface;

abstract class BasePaymentGateway implements PaymentGatewayInterface
{
    protected array $config = [];
    protected bool $testMode = false;

    public function initialize(array $config): self
    {
        $this->config = $config;
        $this->testMode = $config['test_mode'] ?? false;
        return $this;
    }

    abstract public function createOrder(array $orderData): array;
    abstract public function verifyPayment(string $paymentId): array;
    abstract public function refund(string $paymentId, float $amount): array;
    abstract public function getWebhookPayload(): array;
    abstract public function handleWebhook(array $payload): bool;
}
```

### 4.2 RazorPay Update

```php
<?php
// app/PaymentGateway/RazorPayPayment.php

namespace App\PaymentGateway;

use Razorpay\Api\Api;

class RazorPayPayment extends BasePaymentGateway
{
    private Api $razorpay;

    public function initialize(array $config): self
    {
        parent::initialize($config);

        $this->razorpay = new Api(
            $config['key_id'],
            $config['key_secret']
        );

        return $this;
    }

    public function createOrder(array $orderData): array
    {
        $order = $this->razorpay->order->create([
            'receipt' => $orderData['receipt'] ?? uniqid('order_'),
            'amount' => $orderData['amount'] * 100, // Convert to paise
            'currency' => $orderData['currency'] ?? 'INR',
            'notes' => $orderData['notes'] ?? [],
        ]);

        return [
            'success' => true,
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'key_id' => $this->config['key_id'],
        ];
    }

    public function verifyPayment(string $paymentId): array
    {
        try {
            $payment = $this->razorpay->payment->fetch($paymentId);

            return [
                'success' => $payment['status'] === 'captured',
                'payment_id' => $payment['id'],
                'order_id' => $payment['order_id'],
                'amount' => $payment['amount'] / 100,
                'status' => $payment['status'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refund(string $paymentId, float $amount): array
    {
        try {
            $refund = $this->razorpay->refund->create([
                'payment_id' => $paymentId,
                'amount' => $amount * 100,
            ]);

            return [
                'success' => true,
                'refund_id' => $refund['id'],
                'amount' => $refund['amount'] / 100,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getWebhookPayload(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    public function handleWebhook(array $payload): bool
    {
        // Verify webhook signature
        $webhookSecret = $this->config['webhook_secret'] ?? '';
        $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

        $expectedSignature = hash_hmac(
            'sha256',
            json_encode($payload),
            $webhookSecret
        );

        return hash_equals($expectedSignature, $signature);
    }
}
```

### 4.3 PhonePe Integration (NEW)

```bash
composer require phonepe/phonepe-pg-php-sdk
```

```php
<?php
// app/PaymentGateway/PhonePePayment.php

namespace App\PaymentGateway;

use PhonePe\Sdk\Payments\PhonePePaymentClient;
use PhonePe\Sdk\Payments\Models\PaymentRequest;

class PhonePePayment extends BasePaymentGateway
{
    private PhonePePaymentClient $client;

    public function initialize(array $config): self
    {
        parent::initialize($config);

        $this->client = new PhonePePaymentClient(
            merchantId: $config['merchant_id'],
            saltKey: $config['salt_key'],
            saltIndex: $config['salt_index'] ?? 1,
            isProduction: !$this->testMode
        );

        return $this;
    }

    public function createOrder(array $orderData): array
    {
        try {
            $request = PaymentRequest::builder()
                ->merchantTransactionId($orderData['transaction_id'] ?? uniqid('txn_'))
                ->merchantUserId($orderData['user_id'])
                ->amount($orderData['amount'] * 100) // Convert to paise
                ->callbackUrl($orderData['callback_url'])
                ->redirectUrl($orderData['redirect_url'])
                ->redirectMode('POST')
                ->build();

            $response = $this->client->pay($request);

            return [
                'success' => true,
                'payment_url' => $response->getInstrumentResponse()->getRedirectInfo()->getUrl(),
                'transaction_id' => $orderData['transaction_id'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verifyPayment(string $paymentId): array
    {
        try {
            $response = $this->client->checkStatus($paymentId);

            return [
                'success' => $response->getCode() === 'PAYMENT_SUCCESS',
                'transaction_id' => $response->getMerchantTransactionId(),
                'amount' => $response->getAmount() / 100,
                'status' => $response->getCode(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refund(string $paymentId, float $amount): array
    {
        try {
            $response = $this->client->refund(
                merchantTransactionId: $paymentId,
                originalTransactionId: $paymentId,
                amount: $amount * 100
            );

            return [
                'success' => $response->getCode() === 'PAYMENT_SUCCESS',
                'refund_id' => $response->getTransactionId(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getWebhookPayload(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    public function handleWebhook(array $payload): bool
    {
        // Verify callback using checksum
        $xVerify = $_SERVER['HTTP_X_VERIFY'] ?? '';
        $saltKey = $this->config['salt_key'];
        $saltIndex = $this->config['salt_index'] ?? 1;

        $data = base64_encode(json_encode($payload));
        $checksum = hash('sha256', $data . '/pg/v1/status/' . $saltKey) . '###' . $saltIndex;

        return $xVerify === $checksum;
    }
}
```

### 4.4 PayU Integration (NEW)

```bash
composer require payu/payu-checkout-php-sdk
```

```php
<?php
// app/PaymentGateway/PayUPayment.php

namespace App\PaymentGateway;

class PayUPayment extends BasePaymentGateway
{
    private string $baseUrl;

    public function initialize(array $config): self
    {
        parent::initialize($config);

        $this->baseUrl = $this->testMode
            ? 'https://test.payu.in'
            : 'https://secure.payu.in';

        return $this;
    }

    public function createOrder(array $orderData): array
    {
        $txnid = $orderData['transaction_id'] ?? 't' . time() . rand(1000, 9999);

        $hashString = $this->config['merchant_key'] . '|' .
            $txnid . '|' .
            $orderData['amount'] . '|' .
            $orderData['product_info'] . '|' .
            $orderData['firstname'] . '|' .
            $orderData['email'] . '|||||||||||' .
            $this->config['merchant_salt'];

        $hash = strtolower(hash('sha512', $hashString));

        return [
            'success' => true,
            'payment_url' => $this->baseUrl . '/_payment',
            'params' => [
                'key' => $this->config['merchant_key'],
                'txnid' => $txnid,
                'amount' => $orderData['amount'],
                'productinfo' => $orderData['product_info'],
                'firstname' => $orderData['firstname'],
                'email' => $orderData['email'],
                'phone' => $orderData['phone'],
                'surl' => $orderData['success_url'],
                'furl' => $orderData['failure_url'],
                'hash' => $hash,
            ],
        ];
    }

    public function verifyPayment(string $paymentId): array
    {
        $command = 'verify_payment';
        $var1 = $paymentId;

        $hashString = $this->config['merchant_key'] . '|' .
            $command . '|' .
            $var1 . '|' .
            $this->config['merchant_salt'];

        $hash = strtolower(hash('sha512', $hashString));

        $response = $this->makeApiCall('/merchant/postservice.php', [
            'key' => $this->config['merchant_key'],
            'command' => $command,
            'var1' => $var1,
            'hash' => $hash,
        ]);

        $data = json_decode($response, true);

        return [
            'success' => isset($data['status']) && $data['status'] === 1,
            'transaction_id' => $paymentId,
            'data' => $data,
        ];
    }

    public function refund(string $paymentId, float $amount): array
    {
        $command = 'cancel_refund_transaction';

        $hashString = $this->config['merchant_key'] . '|' .
            $command . '|' .
            $paymentId . '|' .
            $this->config['merchant_salt'];

        $hash = strtolower(hash('sha512', $hashString));

        $response = $this->makeApiCall('/merchant/postservice.php', [
            'key' => $this->config['merchant_key'],
            'command' => $command,
            'var1' => $paymentId,
            'var2' => uniqid('refund_'),
            'var3' => $amount,
            'hash' => $hash,
        ]);

        $data = json_decode($response, true);

        return [
            'success' => isset($data['status']) && $data['status'] === 1,
            'data' => $data,
        ];
    }

    public function getWebhookPayload(): array
    {
        return $_POST;
    }

    public function handleWebhook(array $payload): bool
    {
        $status = $payload['status'] ?? '';

        if ($status === 'success') {
            // Verify hash
            $hashString = $this->config['merchant_salt'] . '|' .
                $payload['status'] . '|||||||||||' .
                $payload['email'] . '|' .
                $payload['firstname'] . '|' .
                $payload['productinfo'] . '|' .
                $payload['amount'] . '|' .
                $payload['txnid'] . '|' .
                $this->config['merchant_key'];

            $expectedHash = strtolower(hash('sha512', $hashString));

            return $expectedHash === $payload['hash'];
        }

        return false;
    }

    private function makeApiCall(string $endpoint, array $data): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
```

### 4.5 Payment Gateway Factory

```php
<?php
// app/PaymentGateway/PaymentGatewayFactory.php

namespace App\PaymentGateway;

use App\PaymentGateway\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    private static array $gateways = [
        'razorpay' => RazorPayPayment::class,
        'phonepe' => PhonePePayment::class,
        'payu' => PayUPayment::class,
        'stripe' => StripePayment::class,
        'paypal' => PaypalPayment::class,
        'paystack' => PaystackPayment::class,
        'xendit' => XenditPayment::class,
    ];

    public static function create(string $gateway, array $config = []): PaymentGatewayInterface
    {
        $gateway = strtolower($gateway);

        if (!isset(self::$gateways[$gateway])) {
            throw new InvalidArgumentException("Payment gateway '{$gateway}' is not supported.");
        }

        $gatewayClass = self::$gateways[$gateway];

        return (new $gatewayClass())->initialize($config);
    }

    public static function register(string $name, string $class): void
    {
        self::$gateways[strtolower($name)] = $class;
    }

    public static function available(): array
    {
        return array_keys(self::$gateways);
    }
}
```

### 4.6 Payment Configuration

Add to `.env`:

```env
# RazorPay
RAZORPAY_KEY_ID=your_key_id
RAZORPAY_KEY_SECRET=your_key_secret
RAZORPAY_WEBHOOK_SECRET=your_webhook_secret

# PhonePe
PHONEPE_MERCHANT_ID=your_merchant_id
PHONEPE_SALT_KEY=your_salt_key
PHONEPE_SALT_INDEX=1
PHONEPE_TEST_MODE=true

# PayU
PAYU_MERCHANT_KEY=your_merchant_key
PAYU_MERCHANT_SALT=your_merchant_salt
PAYU_TEST_MODE=true
```

Add to `config/paymentGateway.php`:

```php
<?php

return [
    'default' => env('PAYMENT_GATEWAY', 'razorpay'),

    'gateways' => [
        'razorpay' => [
            'key_id' => env('RAZORPAY_KEY_ID'),
            'key_secret' => env('RAZORPAY_KEY_SECRET'),
            'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
        ],

        'phonepe' => [
            'merchant_id' => env('PHONEPE_MERCHANT_ID'),
            'salt_key' => env('PHONEPE_SALT_KEY'),
            'salt_index' => env('PHONEPE_SALT_INDEX', 1),
            'test_mode' => env('PHONEPE_TEST_MODE', true),
        ],

        'payu' => [
            'merchant_key' => env('PAYU_MERCHANT_KEY'),
            'merchant_salt' => env('PAYU_MERCHANT_SALT'),
            'test_mode' => env('PAYU_TEST_MODE', true),
        ],

        // ... other gateways
    ],
];
```

### 4.7 Database Migration for Payment Gateways

```php
<?php
// database/migrations/2025_11_22_add_new_payment_gateways.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add PhonePe and PayU to payment methods
        DB::table('sm_payment_methods')->insert([
            ['method' => 'PhonePe', 'created_at' => now(), 'updated_at' => now()],
            ['method' => 'PayU', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Add gateway settings columns if needed
        if (!Schema::hasColumn('sm_payment_gateway_settings', 'phonepe_merchant_id')) {
            Schema::table('sm_payment_gateway_settings', function (Blueprint $table) {
                $table->string('phonepe_merchant_id')->nullable();
                $table->string('phonepe_salt_key')->nullable();
                $table->string('phonepe_salt_index')->default('1');
                $table->boolean('phonepe_active')->default(false);

                $table->string('payu_merchant_key')->nullable();
                $table->string('payu_merchant_salt')->nullable();
                $table->boolean('payu_active')->default(false);
            });
        }
    }

    public function down(): void
    {
        DB::table('sm_payment_methods')
            ->whereIn('method', ['PhonePe', 'PayU'])
            ->delete();

        Schema::table('sm_payment_gateway_settings', function (Blueprint $table) {
            $table->dropColumn([
                'phonepe_merchant_id',
                'phonepe_salt_key',
                'phonepe_salt_index',
                'phonepe_active',
                'payu_merchant_key',
                'payu_merchant_salt',
                'payu_active',
            ]);
        });
    }
};
```

---

## Phase 5: Code Modernization

### 5.1 PHP 8.4 Syntax Updates

#### 5.1.1 Constructor Property Promotion

**Before:**
```php
class Student
{
    private int $id;
    private string $name;
    private string $email;

    public function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
}
```

**After:**
```php
class Student
{
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
    ) {}
}
```

#### 5.1.2 Named Arguments

```php
// Before
$student = new Student(1, 'John Doe', 'john@example.com');

// After
$student = new Student(
    id: 1,
    name: 'John Doe',
    email: 'john@example.com',
);
```

#### 5.1.3 Match Expression

**Before:**
```php
switch ($status) {
    case 'pending':
        $label = 'Pending';
        break;
    case 'approved':
        $label = 'Approved';
        break;
    case 'rejected':
        $label = 'Rejected';
        break;
    default:
        $label = 'Unknown';
}
```

**After:**
```php
$label = match($status) {
    'pending' => 'Pending',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    default => 'Unknown',
};
```

#### 5.1.4 Nullsafe Operator

**Before:**
```php
$school = null;
if ($student !== null) {
    if ($student->school !== null) {
        $school = $student->school->name;
    }
}
```

**After:**
```php
$school = $student?->school?->name;
```

### 5.2 Model Updates

#### 5.2.1 Convert $casts to casts() Method

```bash
# Find all models with $casts property
grep -r "protected \$casts" app/
```

Update each model:

```php
// Before
protected $casts = [
    'date_of_birth' => 'date',
    'is_active' => 'boolean',
];

// After
protected function casts(): array
{
    return [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];
}
```

#### 5.2.2 Add Return Types to Relationships

```php
// Before
public function school()
{
    return $this->belongsTo(SmSchool::class, 'school_id');
}

// After
public function school(): BelongsTo
{
    return $this->belongsTo(SmSchool::class, 'school_id');
}
```

### 5.3 Controller Modernization

#### 5.3.1 Add Return Types

```php
// Before
public function index()
{
    return view('students.index');
}

// After
public function index(): View
{
    return view('students.index');
}
```

#### 5.3.2 Type-Hinted Parameters

```php
// Before
public function store(Request $request)
{
    $validated = $request->validate([...]);
}

// After
public function store(StoreStudentRequest $request): RedirectResponse
{
    $validated = $request->validated();
}
```

### 5.4 SmApiController Refactoring

The `SmApiController.php` file is extremely large (900k+ lines). Recommended refactoring:

```
app/Http/Controllers/Api/
├── AuthController.php           # Authentication endpoints
├── StudentController.php        # Student management
├── TeacherController.php        # Teacher management
├── AttendanceController.php     # Attendance endpoints
├── ExamController.php           # Examination endpoints
├── FeesController.php           # Fee management
├── PaymentController.php        # Payment processing
├── ReportController.php         # Report generation
├── NotificationController.php   # Push notifications
├── HomeworkController.php       # Homework management
├── TimetableController.php      # Class schedules
├── LibraryController.php        # Library management
├── TransportController.php      # Transport routes
└── SettingsController.php       # System settings
```

---

## Phase 6: Module Updates

### 6.1 Module Compatibility Check

Each of the 12 modules needs to be updated for Laravel 12 compatibility:

| Module | Priority | Updates Required |
|--------|----------|------------------|
| RolePermission | High | Auth middleware, guards |
| Fees | High | Payment gateway integration |
| Wallet | High | Payment gateway integration |
| Chat | Medium | WebSocket, Pusher updates |
| XenditPayment | Medium | SDK update |
| Lesson | Low | Standard updates |
| BulkPrint | Low | Standard updates |
| MenuManage | Low | Standard updates |
| TemplateSettings | Low | Standard updates |
| HimalayaSms | Low | SMS gateway update |
| StudentAbsentNotification | Low | Standard updates |
| VideoWatch | Low | Standard updates |

### 6.2 Module Service Provider Updates

Each module's service provider needs updating:

```php
<?php
// Modules/RolePermission/Providers/RolePermissionServiceProvider.php

namespace Modules\RolePermission\Providers;

use Illuminate\Support\ServiceProvider;

class RolePermissionServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'RolePermission';
    protected string $moduleNameLower = 'rolepermission';

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    // ... rest of methods
}
```

### 6.3 Module Configuration Updates

Update each module's `module.json`:

```json
{
    "name": "RolePermission",
    "alias": "rolepermission",
    "description": "Role and Permission Management",
    "keywords": [],
    "priority": 0,
    "providers": [
        "Modules\\RolePermission\\Providers\\RolePermissionServiceProvider"
    ],
    "files": [],
    "requires": [
        "laravel/framework:^12.0"
    ]
}
```

---

## Phase 7: Frontend Updates

### 7.1 Build System Migration (Laravel Mix → Vite)

#### 7.1.1 Install Vite

```bash
npm remove laravel-mix
npm install --save-dev vite laravel-vite-plugin
```

#### 7.1.2 Create vite.config.js

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
});
```

#### 7.1.3 Update Blade Templates

```blade
{{-- Before --}}
<link href="{{ mix('css/app.css') }}" rel="stylesheet">
<script src="{{ mix('js/app.js') }}"></script>

{{-- After --}}
@vite(['resources/sass/app.scss', 'resources/js/app.js'])
```

### 7.2 Vue 2 Compatibility

Vue 2 is still supported but in maintenance mode. Consider:

- **Keep Vue 2**: Lower risk, works with current components
- **Upgrade to Vue 3**: Future-proof, requires component rewrites

For now, recommend keeping Vue 2 with `@vitejs/plugin-vue2`.

### 7.3 Update package.json

```json
{
    "private": true,
    "type": "module",
    "scripts": {
        "dev": "vite",
        "build": "vite build"
    },
    "devDependencies": {
        "@vitejs/plugin-vue2": "^2.3.0",
        "axios": "^1.6.0",
        "laravel-vite-plugin": "^1.0.0",
        "sass": "^1.69.0",
        "vite": "^5.0.0",
        "vue": "^2.7.16"
    },
    "dependencies": {
        "bootstrap": "^4.6.2",
        "laravel-echo": "^1.15.0",
        "moment": "^2.29.4",
        "pusher-js": "^8.3.0",
        "v-emoji-picker": "^2.3.3",
        "vue-chat-scroll": "^1.4.0",
        "vue-select": "^3.20.0"
    }
}
```

---

## Phase 8: Testing & Validation

### 8.1 Pre-Testing Checklist

- [ ] All composer packages updated
- [ ] All npm packages updated
- [ ] Migrations run successfully
- [ ] No PHP errors in logs
- [ ] Application boots without errors

### 8.2 Authentication Testing

```bash
# Test session-based auth
php artisan tinker
>>> Auth::attempt(['email' => 'admin@example.com', 'password' => 'password']);

# Test Passport API auth
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### 8.3 Payment Gateway Testing

Test each gateway in sandbox mode:

| Gateway | Test Mode | Test Credentials |
|---------|-----------|------------------|
| RazorPay | `rzp_test_*` | Dashboard test keys |
| PhonePe | Sandbox env | PhonePe test credentials |
| PayU | `https://test.payu.in` | PayU test credentials |
| Stripe | `sk_test_*` | Dashboard test keys |
| PayPal | Sandbox | PayPal sandbox account |
| Paystack | Test mode | Test public/secret keys |

### 8.4 API Endpoint Testing

```bash
# Install API testing tool
composer require --dev laravel/pint

# Create test script
php artisan make:test ApiEndpointTest
```

### 8.5 Module Testing

```bash
# Test each module
php artisan module:test RolePermission
php artisan module:test Fees
php artisan module:test Chat
# ... etc
```

### 8.6 Performance Testing

```bash
# Run Laravel benchmarks
php artisan optimize
php artisan route:cache
php artisan view:cache
php artisan config:cache

# Test response times
ab -n 100 -c 10 http://localhost/
```

### 8.7 Full Test Suite

```bash
php artisan test
php artisan dusk
```

---

## Risk Assessment

### High Risk Items

| Risk | Impact | Mitigation |
|------|--------|------------|
| Passport token incompatibility | Auth fails | Run `passport:install` |
| Payment gateway SDK changes | Payments fail | Test sandbox thoroughly |
| Module incompatibility | Features break | Update modules first |
| Query scope changes | Data issues | Test all scopes |

### Medium Risk Items

| Risk | Impact | Mitigation |
|------|--------|------------|
| Intervention Image API changes | Image upload fails | Update all image code |
| Blade directive changes | Views break | Test all views |
| Route caching issues | 500 errors | Clear caches |

### Low Risk Items

| Risk | Impact | Mitigation |
|------|--------|------------|
| CSS/JS build changes | Styling issues | Rebuild assets |
| Config format changes | Settings lost | Review configs |

---

## Rollback Strategy

### Before Starting

```bash
# Create backup branch
git checkout -b backup/pre-upgrade

# Database backup
mysqldump -u root -p school_db > pre_upgrade_backup.sql

# Full file backup
tar -czvf school_backup.tar.gz /Volumes/Projects/Projects/School
```

### During Migration

```bash
# Commit after each successful phase
git add .
git commit -m "Phase X completed: [description]"
```

### If Rollback Needed

```bash
# Restore code
git checkout backup/pre-upgrade

# Restore database
mysql -u root -p school_db < pre_upgrade_backup.sql

# Clear caches
php artisan optimize:clear
composer dump-autoload
```

---

## Timeline Summary

| Phase | Description | Dependencies |
|-------|-------------|--------------|
| 1 | Environment Preparation | None |
| 2.1 | Laravel 8 → 9 | Phase 1 |
| 2.2 | Laravel 9 → 10 | Phase 2.1 |
| 2.3 | Laravel 10 → 11 | Phase 2.2 |
| 2.4 | Laravel 11 → 12 | Phase 2.3 |
| 3 | Package Updates | Phase 2 |
| 4 | Payment Gateways | Phase 3 |
| 5 | Code Modernization | Phase 3 |
| 6 | Module Updates | Phase 3 |
| 7 | Frontend Updates | Phase 3 |
| 8 | Testing | All phases |

---

## Quick Reference Commands

```bash
# Clear all caches
php artisan optimize:clear

# Update composer dependencies
composer update

# Run migrations
php artisan migrate

# Regenerate autoload
composer dump-autoload

# Clear route cache
php artisan route:clear

# Rebuild assets
npm run build

# Test application
php artisan test
```

---

## Support Resources

- [Laravel 9 Upgrade Guide](https://laravel.com/docs/9.x/upgrade)
- [Laravel 10 Upgrade Guide](https://laravel.com/docs/10.x/upgrade)
- [Laravel 11 Upgrade Guide](https://laravel.com/docs/11.x/upgrade)
- [Laravel 12 Upgrade Guide](https://laravel.com/docs/12.x/upgrade)
- [PHP 8.4 Migration Guide](https://www.php.net/manual/en/migration84.php)
- [Laravel Passport Documentation](https://laravel.com/docs/12.x/passport)
- [RazorPay API Documentation](https://razorpay.com/docs/api/)
- [PhonePe Developer Docs](https://developer.phonepe.com/docs/)
- [PayU Integration Guide](https://developer.payumoney.com/)

---

*Document generated for InfixEdu School Management System upgrade planning.*
