<?php
declare(strict_types=1);

namespace App\Core;

final class Validator
{
    private array $errors = [];

    public function required(string $field, string $value, string $message = 'Required'): self
    {
        if (trim($value) === '') $this->errors[$field] = $message;
        return $this;
    }

    public function email(string $field, string $value, string $message = 'Invalid email'): self
    {
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function minLen(string $field, string $value, int $min, string $message = ''): self
    {
        if ($value !== '' && mb_strlen($value) < $min) {
            $this->errors[$field] = $message !== '' ? $message : "Minimum {$min} characters";
        }
        return $this;
    }

    public function ok(): bool
    {
        return empty($this->errors);
    }

    /** @return array<string,string> */
    public function errors(): array
    {
        return $this->errors;
    }
}
