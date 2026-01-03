<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Supported locales
     */
    protected array $supportedLocales = ['en', 'fr', 'ar'];

    /**
     * Switch locale
     */
    public function switch(Request $request, string $locale)
    {
        // Validate locale is supported
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = config('app.locale');
        }

        // Store in session
        session(['locale' => $locale]);

        // Redirect back with cookie
        return redirect()
            ->back()
            ->withCookie(cookie()->forever('locale', $locale));
    }
}
