<?php
declare(strict_types=1);

namespace App\Core;

final class Env
{
    private static array $vars = [];

    public static function load(string $path): void
    {
        if (!is_file($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) return;

        foreach ($lines as $line) {
            $t = trim($line);
            if ($t === '' || str_starts_with($t, '#')) continue;

            $pos = strpos($t, '=');
            if ($pos === false) continue;

            $key = trim(substr($t, 0, $pos));
            $val = trim(substr($t, $pos + 1));
            $val = self::stripQuotes($val);

            self::$vars[$key] = $val;
            if (getenv($key) === false) putenv($key . '=' . $val);
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        if (isset(self::$vars[$key])) return self::$vars[$key];
        $env = getenv($key);
        if ($env !== false) return (string)$env;
        return $default;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $v = strtolower(self::get($key, $default ? '1' : '0'));
        return in_array($v, ['1','true','yes','on'], true);
    }

    private static function stripQuotes(string $val): string
    {
        $len = strlen($val);
        if ($len >= 2) {
            $a = $val[0];
            $b = $val[$len - 1];
            if (($a === '"' && $b === '"') || ($a === "'" && $b === "'")) {
                return substr($val, 1, $len - 2);
            }
        }
        return $val;
    }
}
