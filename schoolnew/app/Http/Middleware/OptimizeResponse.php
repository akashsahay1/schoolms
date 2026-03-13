<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip for API responses or file downloads
        if ($request->expectsJson() || $response->headers->get('Content-Disposition')) {
            return $response;
        }

        // Add security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Add performance headers for cacheable responses
        if ($request->isMethod('GET') && $response->isSuccessful()) {
            // Don't cache authenticated responses by default
            if (!$request->user()) {
                // For public pages, allow some caching
                if ($this->isCacheablePage($request)) {
                    $response->headers->set('Cache-Control', 'public, max-age=300, must-revalidate');
                }
            } else {
                // For authenticated pages, use private cache
                $response->headers->set('Cache-Control', 'private, no-cache, must-revalidate');
            }
        }

        return $response;
    }

    /**
     * Check if the page is cacheable
     */
    protected function isCacheablePage(Request $request): bool
    {
        $cacheablePaths = [
            '/',
            '/about',
            '/contact',
            '/academics',
            '/gallery',
            '/events',
            '/news',
        ];

        return in_array($request->path(), $cacheablePaths) ||
               str_starts_with($request->path(), 'website/');
    }
}
