<?php

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'layouts/main'): Response
    {
        $html = View::render($view, $data, $layout);
        return Response::make($html, 200);
    }

    protected function json(mixed $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    protected function redirect(string $url, array $flash = []): Response
    {
        return Response::redirect($url, $flash);
    }

    protected function validate(Request $request, array $rules): array
    {
        $validator = Validator::make($request->all(), $rules);

        if (!$validator->validate()) {
            Session::setFlash('_errors', $validator->errors());
            Session::setFlash('_old_input', $request->all());
            Session::setFlash('error', $validator->firstError() ?? 'Maʼlumotlarni kiritishda xatolik yuz berdi.');

            // Store validation exception to be caught or handled
            throw new \InvalidArgumentException($validator->firstError() ?? 'Validatsiya xatosi');
        }

        return $request->all();
    }

    protected function user(): ?array
    {
        return Auth::user();
    }

    protected function userId(): ?int
    {
        return Auth::id();
    }

    protected function isAdmin(): bool
    {
        return Auth::isAdmin();
    }

    protected function isManager(): bool
    {
        return Auth::isManager();
    }

    /**
     * IDOR (Insecure Direct Object Reference) prevention:
     * Check if current user is admin OR owns the resource.
     */
    protected function authorizeResource(int $resourceOwnerId, string $errorMessage = 'Sizda ushbu maʼlumotni koʻrish yoki tahrirlash ruxsati yoʻq.'): void
    {
        if ($this->isAdmin()) {
            return;
        }

        if ($this->userId() !== $resourceOwnerId) {
            $html = View::render('errors.403', [
                'message' => $errorMessage
            ], null);
            Response::make($html, 403)->send();
            exit;
        }
    }
}
