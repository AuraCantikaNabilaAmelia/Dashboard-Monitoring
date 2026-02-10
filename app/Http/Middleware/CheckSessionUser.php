<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user')) {
            return redirect('/login')->withErrors(['session' => 'Silakan login terlebih dahulu']);
        }

        return $next($request);
    }
}
