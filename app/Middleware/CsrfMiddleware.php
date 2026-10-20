<?php

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

class CsrfMiddleware implements Middleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        $method = $request->method();

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $request->csrfToken();

            if (!Csrf::validate($token)) {
                $html = View::render('errors.419', [
                    'message' => 'Xavfsizlik belgisi (CSRF token) muddati tugagan yoki yaroqsiz. Sahifani qayta yuklang.'
                ], null);
                return Response::make($html, 419);
            }
        }

        return $next($request);
    }
}
