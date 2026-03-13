<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only force HTTPS in production
        if (App::environment('production')) {
            // Check if request is not secure and not coming from a trusted proxy with HTTPS
            if (!$request->secure() && !$this->isSecureFromProxy($request)) {
                // Redirect to HTTPS
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        return $next($request);
    }

    /**
     * Check if request is secure via a proxy
     */
    protected function isSecureFromProxy(Request $request): bool
    {
        // Check common headers set by load balancers and proxies
        $secureHeaders = [
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Ssl' => 'on',
            'X-Url-Scheme' => 'https',
        ];

        foreach ($secureHeaders as $header => $value) {
            if ($request->header($header) === $value) {
                return true;
            }
        }

        return false;
    }
}
