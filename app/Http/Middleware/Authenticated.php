<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class Authenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check both web and machine guards since login uses specific guards
        if (!Auth::guard('web')->check() && !Auth::guard('machine')->check()) {
            return Redirect::route('login')->with('error', 'You are not logged in.');
        }
        return $next($request);
    }
}
