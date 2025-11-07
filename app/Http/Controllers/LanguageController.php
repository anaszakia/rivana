<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    /**
     * Change the application language
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request)
    {
        $locale = $request->input('locale', 'id');
        
        // Log the switch attempt
        Log::info('Language switch attempted', [
            'requested_locale' => $locale,
            'current_locale' => app()->getLocale(),
            'session_before' => session('locale'),
            'cookie_before' => $request->cookie('locale')
        ]);
        
        // Validate locale
        if (!in_array($locale, ['en', 'id'])) {
            $locale = 'id';
        }
        
        // Store locale in session
        $request->session()->put('locale', $locale);
        $request->session()->save(); // Force save session
        
        // Also set it immediately for this request
        app()->setLocale($locale);
        
        // Log success
        Log::info('Language switch completed', [
            'new_locale' => $locale,
            'session_after' => session('locale')
        ]);
        
        // Return with cookie that lasts 1 year
        return redirect()->back()->cookie('locale', $locale, 525600);
    }
}
