<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    private const SESSION_USER_KEY = '_auth_user_id';
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public static function check(): bool
    {
        return Session::has(self::SESSION_USER_KEY);
    }

    public static function id(): ?int
    {
        return Session::get(self::SESSION_USER_KEY);
    }

    public static function user(): ?array
    {
        $id = self::id();
        if (!$id) {
            return null;
        }

        static $cachedUser = null;
        if ($cachedUser !== null && ($cachedUser['id'] ?? null) === $id) {
            return $cachedUser;
        }

        $userModel = new User();
        $cachedUser = $userModel->find($id);

        if (!$cachedUser || empty($cachedUser['is_active'])) {
            self::logout();
            return null;
        }

        return $cachedUser;
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user['role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isManager(): bool
    {
        return self::role() === 'manager';
    }

    public static function attempt(string $email, string $password, string $ip): array
    {
        // 1. Rate limiting check
        if (self::isLockedOut($ip, $email)) {
            return [
                'success' => false,
                'message' => 'Juda koʻp muvaffaqiyatsiz urinishlar! Xavfsizlik maqsadida tizim ' . self::LOCKOUT_MINUTES . ' daqiqaga bloklandi.'
            ];
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            self::recordFailedAttempt($ip, $email);
            return [
                'success' => false,
                'message' => 'Email yoki parol notoʻgʻri.'
            ];
        }

        if (empty($user['is_active'])) {
            return [
                'success' => false,
                'message' => 'Ushbu hisob faol emas. Administrator bilan bogʻlaning.'
            ];
        }

        if (!password_verify($password, $user['password'])) {
            self::recordFailedAttempt($ip, $email);
            return [
                'success' => false,
                'message' => 'Email yoki parol notoʻgʻri.'
            ];
        }

        // Check if password needs rehash
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $userModel->update($user['id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
        }

        // Login success: Clear attempts, regenerate session
        self::clearFailedAttempts($ip, $email);
        Session::regenerate();
        Session::set(self::SESSION_USER_KEY, (int)$user['id']);

        return [
            'success' => true,
            'user' => $user
        ];
    }

    public static function logout(): void
    {
        Session::remove(self::SESSION_USER_KEY);
        Session::regenerate();
    }

    public static function isLockedOut(string $ip, string $email): bool
    {
        $db = Database::connect();
        try {
            $stmt = $db->prepare(
                "SELECT attempts, last_attempt_at FROM login_attempts WHERE (ip_address = :ip OR email = :email) LIMIT 1"
            );
            $stmt->execute(['ip' => $ip, 'email' => $email]);
            $record = $stmt->fetch();

            if ($record && (int)$record['attempts'] >= self::MAX_ATTEMPTS) {
                $lastAttempt = strtotime($record['last_attempt_at']);
                $lockoutSeconds = self::LOCKOUT_MINUTES * 60;
                if ((time() - $lastAttempt) < $lockoutSeconds) {
                    return true;
                }
                // Lockout period has passed, reset counter
                self::clearFailedAttempts($ip, $email);
            }
        } catch (\Throwable $e) {
            // Fail open if table doesn't exist yet during initial setup
            error_log('Rate limiting check failed: ' . $e->getMessage());
        }

        return false;
    }

    public static function recordFailedAttempt(string $ip, string $email): void
    {
        $db = Database::connect();
        try {
            $stmt = $db->prepare(
                "INSERT INTO login_attempts (ip_address, email, attempts, last_attempt_at) 
                 VALUES (:ip, :email, 1, NOW())
                 ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt_at = NOW()"
            );
            $stmt->execute(['ip' => $ip, 'email' => $email]);
        } catch (\Throwable $e) {
            error_log('Record failed attempt error: ' . $e->getMessage());
        }
    }

    public static function clearFailedAttempts(string $ip, string $email): void
    {
        $db = Database::connect();
        try {
            $stmt = $db->prepare(
                "DELETE FROM login_attempts WHERE ip_address = :ip OR email = :email"
            );
            $stmt->execute(['ip' => $ip, 'email' => $email]);
        } catch (\Throwable $e) {
            error_log('Clear failed attempts error: ' . $e->getMessage());
        }
    }
}
