<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is stored in session
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
            
            // Validate locale
            if (in_array($locale, config('app.available_locales', ['tr', 'en']))) {
                app()->setLocale($locale);
            }
        } elseif ($request->user() && $request->user()->preferred_locale) {
            // Set locale from user preference
            app()->setLocale($request->user()->preferred_locale);
        }
        
        return $next($request);
    }
}
