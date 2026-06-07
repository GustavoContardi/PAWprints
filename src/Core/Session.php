<?php

namespace Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = (($_ENV['APP_ENV'] ?? 'development') === 'production');
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    public static function csrfToken(): string
    {
        self::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrf(?string $token): bool
    {
        self::start();
        if ($token === null || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function login(int $employeeId, string $employeeName): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['employee_id'] = $employeeId;
        $_SESSION['employee_name'] = $employeeName;
    }

    public static function isAuthenticated(): bool
    {
        self::start();
        return !empty($_SESSION['employee_id']);
    }

    public static function employeeName(): ?string
    {
        self::start();
        return $_SESSION['employee_name'] ?? null;
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
