<?php

declare(strict_types=1);

namespace GkiMenteng\Core;

final class Request
{
    /**
     * @param array<string, string> $query
     * @param array<string, string> $cookies
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query = [],
        public readonly ?string $body = null,
        public readonly array $cookies = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        $query = [];
        foreach ($_GET as $key => $value) {
            if (is_string($key) && (is_string($value) || is_numeric($value))) {
                $query[$key] = (string) $value;
            }
        }

        $body = file_get_contents('php://input') ?: null;

        $cookies = [];
        foreach ($_COOKIE as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $cookies[$key] = $value;
            }
        }

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $path,
            $query,
            $body,
            $cookies,
        );
    }

    public function cookie(string $name): ?string
    {
        return $this->cookies[$name] ?? null;
    }

    public function bearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!is_string($header) || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($header, 7));

        return $token !== '' ? $token : null;
    }

    public function query(string $key, ?string $default = null): ?string
    {
        return $this->query[$key] ?? $default;
    }

    /** @return array<string, mixed> */
    public function json(): array
    {
        if ($this->body === null || $this->body === '') {
            return [];
        }

        $decoded = json_decode($this->body, true);

        return is_array($decoded) ? $decoded : [];
    }
}
