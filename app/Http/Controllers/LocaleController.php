<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');
        $available = config('app.available_locales', ['en', 'kh']);

        if (! in_array($locale, $available, true)) {
            $locale = config('app.locale', 'en');
        }

        $request->session()->put('locale', $locale);
        app()->setLocale($locale);

        return back();
    }
}
