<?php
declare(strict_types=1);

namespace App\Core;

final class Response
{
    private function __construct(
        private string $body,
        private int $status = 200,
        private array $headers = ['Content-Type' => 'text/html; charset=utf-8']
    ) {}

    public static function html(string $html, int $status = 200): self
    {
        return new self($html, $status);
    }

    public static function redirect(string $to): self
    {
        return new self('', 302, ['Location' => $to]);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $k => $v) header($k . ': ' . $v);
        echo $this->body;
    }
}
