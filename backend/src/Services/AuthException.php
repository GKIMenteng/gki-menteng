<?php

declare(strict_types=1);

namespace GkiMenteng\Services;

final class AuthException extends \RuntimeException
{
    /** @param array<string, string> $errors */
    public function __construct(
        string $message,
        private readonly int $statusCode = 400,
        private readonly array $errors = [],
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /** @return array<string, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
