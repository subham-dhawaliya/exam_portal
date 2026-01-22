<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended.');
        }

        return $next($request);
    }
}
