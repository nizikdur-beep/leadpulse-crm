<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

class AdminMiddleware implements Middleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            $html = View::render('errors.403', [
                'message' => 'Ushbu sahifaga kirish faqat Administrator uchun ruxsat etilgan.'
            ], null);
            return Response::make($html, 403);
        }

        return $next($request);
    }
}
