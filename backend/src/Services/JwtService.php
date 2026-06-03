<?php

declare(strict_types=1);

namespace GkiMenteng\Services;

final class JwtService
{
    public function __construct(
        private readonly string $secret,
        private readonly int $accessTtlSeconds = 900,
    ) {
    }

    /** @param array<string, mixed> $claims */
    public function issueAccessToken(int $userId, string $role): string
    {
        $now = time();

        return $this->encode([
            'sub' => $userId,
            'role' => $role,
            'type' => 'access',
            'iat' => $now,
            'exp' => $now + $this->accessTtlSeconds,
        ]);
    }

    public function getAccessTtlSeconds(): int
    {
        return $this->accessTtlSeconds;
    }

    /** @return array{sub: int, role: string, type: string}|null */
    public function decodeAccessToken(string $token): ?array
    {
        $payload = $this->decode($token);
        if ($payload === null) {
            return null;
        }

        if (($payload['type'] ?? '') !== 'access') {
            return null;
        }

        $sub = $payload['sub'] ?? null;
        if (!is_int($sub) && !(is_string($sub) && ctype_digit($sub))) {
            return null;
        }

        return [
            'sub' => (int) $sub,
            'role' => (string) ($payload['role'] ?? 'member'),
            'type' => 'access',
        ];
    }

    /** @param array<string, mixed> $payload */
    private function encode(array $payload): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
        $body = $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', "{$header}.{$body}", $this->secret, true),
        );

        return "{$header}.{$body}.{$signature}";
    }

    /** @return array<string, mixed>|null */
    private function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;
        $expected = $this->base64UrlEncode(
            hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $this->secret, true),
        );

        if (!hash_equals($expected, $signatureB64)) {
            return null;
        }

        $headerJson = $this->base64UrlDecode($headerB64);
        $payloadJson = $this->base64UrlDecode($payloadB64);
        if ($headerJson === false || $payloadJson === false) {
            return null;
        }

        try {
            $header = json_decode($headerJson, true, 512, JSON_THROW_ON_ERROR);
            $payload = json_decode($payloadJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        if (!is_array($header) || ($header['alg'] ?? '') !== 'HS256' || !is_array($payload)) {
            return null;
        }

        $exp = $payload['exp'] ?? null;
        if (!is_int($exp) || $exp < time()) {
            return null;
        }

        return $payload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string|false
    {
        $padding = 4 - (strlen($data) % 4);
        if ($padding < 4) {
            $data .= str_repeat('=', $padding);
        }

        return base64_decode(strtr($data, '-_', '+/'), true);
    }
}
