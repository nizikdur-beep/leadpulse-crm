<?php

namespace App\Core;

// Global helper functions for views
if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return \App\Core\Csrf::field();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return \App\Core\Csrf::getToken();
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        $old = \App\Core\Session::getFlash('_old_input', []);
        return $old[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $default = null): mixed
    {
        return \App\Core\Session::getFlash($key, $default);
    }
}

if (!function_exists('has_flash')) {
    function has_flash(string $key): bool
    {
        return \App\Core\Session::hasFlash($key);
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        return \App\Core\Auth::user();
    }
}

class View
{
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Koʻrinish (View) fayli topilmadi: {$viewPath}");
        }

        // Merge shared data, local data
        $mergedData = array_merge(self::$shared, $data);
        extract($mergedData, EXTR_SKIP);

        // Capture view content
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // If no layout is requested, return content directly
        if ($layout === null) {
            return $content;
        }

        // Render within layout
        $layoutFile = __DIR__ . '/../Views/' . str_replace('.', '/', $layout) . '.php';
        if (!file_exists($layoutFile)) {
            return $content;
        }

        ob_start();
        include $layoutFile;
        return ob_get_clean();
    }
}
