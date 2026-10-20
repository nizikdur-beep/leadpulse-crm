<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;

class GuestMiddleware implements Middleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (Auth::check() && Auth::user()) {
            return Response::redirect('/dashboard');
        }

        return $next($request);
    }
}
