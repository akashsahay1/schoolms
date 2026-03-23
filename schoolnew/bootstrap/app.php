<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redirect authenticated users away from guest-only pages (login, register, etc.)
        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            $user = $request->user();
            if (!$user) {
                return '/';
            }

            // Check if user is a student
            if (\App\Models\Student::where('user_id', $user->id)->exists()) {
                return route('portal.dashboard');
            }

            // Check if user is a parent
            if (\App\Models\ParentGuardian::where('user_id', $user->id)
                ->orWhere('father_email', $user->email)
                ->orWhere('mother_email', $user->email)
                ->orWhere('guardian_email', $user->email)
                ->exists()) {
                return route('portal.dashboard');
            }

            // Check if user is staff with admin panel role
            if ($user->hasAnyRole(['Super Admin', 'Admin', 'Accountant', 'Librarian', 'Receptionist'])) {
                return route('admin.dashboard');
            }

            // Check if user is staff (teacher/other)
            if (\App\Models\Staff::where('user_id', $user->id)->exists()) {
                return route('teacher.dashboard');
            }

            return route('admin.dashboard');
        });

        // Add security headers and HTTPS redirect to all web requests
        $middleware->web(append: [
            \App\Http\Middleware\ForceHttps::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Exclude webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'razorpay/webhook',
        ]);

        // Alias for rate limiting and role checking
        $middleware->alias([
            'throttle.logins' => \App\Http\Middleware\ThrottleLogins::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
