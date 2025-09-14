<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add security headers if HTTPS should be enforced
        if ($this->shouldEnforceHttps($request)) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->headers->set('Content-Security-Policy', 'upgrade-insecure-requests');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        return $response;
    }

    protected function shouldEnforceHttps(Request $request): bool
    {
        // Don't enforce HTTPS during unit tests
        if (app()->runningUnitTests()) {
            return false;
        }

        // Check if we're in a production environment
        if (app()->environment(['production', 'staging'])) {
            return true;
        }

        // Check if the current request is already HTTPS
        if ($request->isSecure()) {
            return true;
        }

        // Check for DigitalOcean App Platform indicators
        if ($request->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            return true;
        }

        // Check if APP_URL is HTTPS
        $appUrl = config('app.url');
        if ($appUrl && str_starts_with($appUrl, 'https://')) {
            return true;
        }

        return false;
    }
}
