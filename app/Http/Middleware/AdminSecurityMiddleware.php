<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

class AdminSecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Add security headers
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Log sensitive admin access (GET requests to dashboard or management pages)
        if ($request->isMethod('GET') && $request->route() && str_contains($request->route()->getName(), 'admin.')) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'type' => 'ACCESS_ADMIN_PAGE',
                'description' => 'Accessed: ' . $request->fullUrl(),
                'ip_address' => $request->ip(),
                'metadata' => ['user_agent' => $request->userAgent()],
            ]);
        }

        return $response;
    }
}
