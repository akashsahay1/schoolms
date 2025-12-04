<?php

namespace App\Http;

use App\Http\Middleware\HttpsProtocol;
use App\Http\Middleware\UserRolePermission;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\HttpsProtocol::class,
            \App\Http\Middleware\Localization::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'RouteServe' => \App\Http\Middleware\RouteServe::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'CheckUserMiddleware' => \App\Http\Middleware\CheckUserMiddleware::class,
        'CheckDashboardMiddleware' => \App\Http\Middleware\CheckDashboardMiddleware::class,
        'StudentMiddleware' => \App\Http\Middleware\StudentMiddleware::class,
        'ParentMiddleware' => \App\Http\Middleware\ParentMiddleware::class,
        'CustomerMiddleware' => \App\Http\Middleware\CustomerMiddleware::class,
        'PM' => \App\Http\Middleware\ProductMiddleware::class,
        'cors' => \App\Http\Middleware\Cors::class,
        'XSS' => \App\Http\Middleware\XSS::class,
        'SAMiddleware' => \App\Http\Middleware\SAMiddleware::class,
        'subscriptionAccessUrl' => \App\Http\Middleware\SubscriptionAccessUrl::class,
        'userRolePermission' => UserRolePermission::class,
        'json.response' => \App\Http\Middleware\ForceJsonResponse::class,
        'subdomain' => \App\Http\Middleware\SubdomainMiddleware::class,
        'module' => \App\Http\Middleware\ModulePermissionMiddleware::class,
    ];
}
