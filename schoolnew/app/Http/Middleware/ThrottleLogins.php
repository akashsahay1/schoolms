<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLogins
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestKey($request);

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
                'retry_after' => $seconds,
            ], 429);
        }

        $response = $next($request);

        // If login failed (401/422), increment attempts
        if ($response->getStatusCode() === 401 || $response->getStatusCode() === 422) {
            RateLimiter::hit($key, $this->decayMinutes() * 60);
        } else {
            // Clear attempts on successful login
            RateLimiter::clear($key);
        }

        return $response;
    }

    /**
     * Resolve the rate limit key for the request.
     */
    protected function resolveRequestKey(Request $request): string
    {
        return 'login:' . sha1($request->ip() . '|' . strtolower($request->input('email', '')));
    }

    /**
     * Maximum login attempts allowed.
     */
    protected function maxAttempts(): int
    {
        return 5;
    }

    /**
     * Decay minutes for rate limiter.
     */
    protected function decayMinutes(): int
    {
        return 15;
    }
}
