<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTrial
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->trial_ends_at && now()->isAfter($user->trial_ends_at)) {
            if (!$request->routeIs('trial.expired') && !$request->routeIs('logout')) {
                return redirect()->route('trial.expired');
            }
        }

        return $next($request);
    }
}
