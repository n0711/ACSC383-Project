<?php
declare(strict_types=1);

namespace App\Core;

final class Helpers
{
    
    public static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function route(string $key, array $params = []): string
    {
        $url = rtrim(APP_URL, '/') . '/index.php?r=' . urlencode($key);

        foreach ($params as $k => $v) {
            $url .= '&' . urlencode((string)$k) . '=' . urlencode((string)$v);
        }

        return $url;
    }


    public static function asset(string $path): string
    {
        return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
    }


    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'][] = [
            'type' => $type,
            'message' => $message
        ];
    }


    public static function consumeFlash(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return is_array($messages) ? $messages : [];
    }
}
