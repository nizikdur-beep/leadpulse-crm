<?php

namespace App\Core;

class Csrf
{
    private const TOKEN_KEY = '_csrf_token';

    public static function getToken(): string
    {
        Session::start();
        $token = Session::get(self::TOKEN_KEY);
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::TOKEN_KEY, $token);
        }
        return $token;
    }

    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        $sessionToken = Session::get(self::TOKEN_KEY);
        if (!$sessionToken) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    public static function regenerate(): void
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set(self::TOKEN_KEY, $token);
    }
}
