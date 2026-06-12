<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // Owner account has trial_ends_at = NULL
        if (!$request->user() || $request->user()->trial_ends_at !== null) {
            abort(403);
        }

        return $next($request);
    }
}
