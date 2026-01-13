<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private const KEY = 'auth_user';

    public static function check(): bool
    {
        return isset($_SESSION[self::KEY]) && is_array($_SESSION[self::KEY]);
    }

    /** @return array<string,mixed>|null */
    public static function user(): ?array
    {
        return self::check() ? $_SESSION[self::KEY] : null;
    }

    public static function id(): ?int
    {
        $u = self::user();
        if (!$u) return null;
        $id = $u['user_id'] ?? null;
        return is_int($id) ? $id : (is_string($id) ? (int)$id : null);
    }

    public static function login(array $user): void
    {
        // Store minimal session 
        $_SESSION[self::KEY] = [
            'user_id' => (int)$user['user_id'],
            'display_name' => (string)$user['display_name'],
            'university_email' => (string)$user['university_email'],
            'is_verified' => (int)($user['is_verified'] ?? 0),
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION[self::KEY]);
    }
}
