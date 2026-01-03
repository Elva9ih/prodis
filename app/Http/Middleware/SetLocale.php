<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales
     */
    protected array $supportedLocales = ['en', 'fr', 'ar'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for locale in session, cookie, or default to config
        $locale = session('locale', $request->cookie('locale', config('app.locale')));

        // Validate locale is supported
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        // Share locale and direction with all views
        $isRtl = $locale === 'ar';
        view()->share('currentLocale', $locale);
        view()->share('isRtl', $isRtl);
        view()->share('supportedLocales', $this->supportedLocales);

        return $next($request);
    }
}
