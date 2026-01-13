<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    private function __construct(
        private array $get,
        private array $post,
        private array $server
    ) {}

    public static function fromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function isPost(): bool
    {
        return strtoupper($this->method()) === 'POST';
    }

    public function route(): string
    {
        $r = $this->get['r'] ?? 'landing';
        return is_string($r) && $r !== '' ? $r : 'landing';
    }

    public function input(string $key, string $default = ''): string
    {
        $val = $this->post[$key] ?? $this->get[$key] ?? $default;
        return is_string($val) ? trim($val) : $default;
    }
}
