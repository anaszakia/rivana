<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Priority: 
        // 1. Session locale
        // 2. Cookie locale  
        // 3. Default from config
        
        $locale = Session::get('locale');
        
        if (!$locale) {
            $locale = $request->cookie('locale');
        }
        
        if (!$locale) {
            $locale = config('app.locale');
        }
        
        // Validate locale (only allow 'en' and 'id')
        if (!in_array($locale, ['en', 'id'])) {
            $locale = config('app.locale');
        }
        
        // Set the application locale
        App::setLocale($locale);
        
        return $next($request);
    }
}
