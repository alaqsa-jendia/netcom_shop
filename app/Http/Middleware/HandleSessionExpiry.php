<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSessionExpiry
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('locale') && $request->session()->has('_token')) {
            $request->session()->put('locale', config('app.locale'));
        }

        return $next($request);
    }
}