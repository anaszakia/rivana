<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Allow iframe for file preview (especially HTML maps)
        // Don't set X-Frame-Options for file preview routes
        if (!$request->is('hidrologi/file/preview/*')) {
            // Prevent clickjacking for all other routes
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN'); // Changed from DENY to SAMEORIGIN
        }
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // XSS Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Content Security Policy
        // Allow frame-ancestors for file preview routes (for iframe embedding)
        if ($request->is('hidrologi/file/preview/*')) {
            $response->headers->set('Content-Security-Policy', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://unpkg.com https://*.openstreetmap.org https://*.tile.openstreetmap.org; " .
                "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://unpkg.com https://*.openstreetmap.org; " .
                "img-src 'self' data: https: blob:; " .
                "font-src 'self' https://cdnjs.cloudflare.com; " .
                "connect-src 'self' https://nominatim.openstreetmap.org https://unpkg.com https://*.openstreetmap.org https://*.tile.openstreetmap.org; " .
                "frame-ancestors 'self';" // Allow same-origin iframe
            );
        } else {
            // For show page, allow iframe-src for embedding
            $response->headers->set('Content-Security-Policy', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://unpkg.com; " .
                "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://unpkg.com; " .
                "img-src 'self' data: https:; " .
                "font-src 'self' https://cdnjs.cloudflare.com; " .
                "connect-src 'self' https://nominatim.openstreetmap.org https://unpkg.com; " .
                "frame-src 'self'; " . // IMPORTANT: Allow same-origin iframes
                "frame-ancestors 'self';" // Allow this page to be iframed by same origin
            );
        }

        return $response;
    }
}
