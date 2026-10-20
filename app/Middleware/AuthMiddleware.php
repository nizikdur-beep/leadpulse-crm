<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()) {
            return Response::redirect('/login', [
                'error' => 'Tizimdan foydalanish uchun tizimga kiring.'
            ]);
        }

        return $next($request);
    }
}
