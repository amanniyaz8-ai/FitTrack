<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', 'ru');
        if (!in_array($locale, ['ru', 'kk'])) {
            $locale = 'ru';
        }
        app()->setLocale($locale);
        return $next($request);
    }
}
