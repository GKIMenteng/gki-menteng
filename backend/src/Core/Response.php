<?php

declare(strict_types=1);

namespace GkiMenteng\Core;

final class Response
{
    /** @param array<string, string> $headers */
    public function __construct(
        private readonly int $status,
        private readonly mixed $body,
        private readonly array $headers = ['Content-Type' => 'application/json; charset=utf-8'],
    ) {
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self($status, $data);
    }

    public static function error(string $message, int $status = 400, ?array $details = null): self
    {
        $payload = ['success' => false, 'message' => $message];
        if ($details !== null) {
            $payload['errors'] = $details;
        }

        return new self($status, $payload);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    /** @return array<string, string> */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        if (is_array($this->body) || is_object($this->body)) {
            echo json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            return;
        }

        echo (string) $this->body;
    }
}
