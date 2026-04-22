<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExcludeCsrfForTelegram
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow without CSRF token
        return $next($request);
    }
}
