<?php

declare(strict_types=1);

namespace GkiMenteng\Services;

use GkiMenteng\Config\Env;
use GkiMenteng\Core\Request;
use GkiMenteng\Repositories\RefreshTokenRepository;
use GkiMenteng\Repositories\UserRepository;

final class AuthService
{
    private const REFRESH_COOKIE = 'gki_refresh_token';
    private const REFRESH_COOKIE_PATH = '/api/auth';

    private readonly int $refreshTtlSeconds;
    private readonly JwtService $jwt;

    public function __construct(
        private readonly UserRepository $users = new UserRepository(),
        private readonly RefreshTokenRepository $refreshTokens = new RefreshTokenRepository(),
    ) {
        $this->jwt = new JwtService(
            (string) Env::get('JWT_SECRET', 'change-me-in-production'),
            (int) Env::get('JWT_ACCESS_TTL', 900),
        );
        $fromEnv = Env::get('JWT_REFRESH_TTL');
        $this->refreshTtlSeconds = $fromEnv !== null && $fromEnv !== ''
            ? (int) $fromEnv
            : 604800;
    }

    public function getRefreshTtlSeconds(): int
    {
        return $this->refreshTtlSeconds;
    }

    /** @return array{user: array, tokens: array, cookie: string|null} */
    public function register(string $name, string $email, string $password): array
    {
        if ($this->users->emailExists($email)) {
            throw new AuthException('Email sudah terdaftar.', 422, ['email' => 'Email sudah digunakan.']);
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new AuthException('Gagal memproses password.', 500);
        }

        $user = $this->users->create($name, $email, $hash);

        return $this->issueTokensForUser($user);
    }

    /** @return array{user: array, tokens: array, cookie: string|null} */
    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);
        if ($user === null || !password_verify($password, $user['passwordHash'])) {
            throw new AuthException('Email atau kata sandi salah.', 401);
        }

        unset($user['passwordHash']);

        return $this->issueTokensForUser($user);
    }

    /** @return array{user: array, tokens: array, cookie: string|null} */
    public function refresh(Request $request): array
    {
        $plain = $this->resolveRefreshToken($request);
        if ($plain === null) {
            throw new AuthException('Sesi tidak valid. Silakan masuk kembali.', 401);
        }

        $record = $this->refreshTokens->findValid(hash('sha256', $plain));
        if ($record === null) {
            throw new AuthException('Sesi tidak valid. Silakan masuk kembali.', 401);
        }

        $this->refreshTokens->revoke($record['id']);

        $user = $this->users->findById($record['userId']);
        if ($user === null) {
            throw new AuthException('Akun tidak ditemukan.', 401);
        }

        return $this->issueTokensForUser($user);
    }

    public function logout(Request $request): ?string
    {
        $plain = $this->resolveRefreshToken($request);
        if ($plain !== null) {
            $record = $this->refreshTokens->findValid(hash('sha256', $plain));
            if ($record !== null) {
                $this->refreshTokens->revoke($record['id']);
            }
        }

        return $this->clearRefreshCookie();
    }

    public function userFromRequest(Request $request): ?array
    {
        $token = $request->bearerToken();
        if ($token === null) {
            return null;
        }

        $claims = $this->jwt->decodeAccessToken($token);
        if ($claims === null) {
            return null;
        }

        return $this->users->findById($claims['sub']);
    }

    public static function validatePassword(string $password): ?string
    {
        if (strlen($password) < 8) {
            return 'Kata sandi minimal 8 karakter.';
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            return 'Kata sandi harus mengandung huruf dan angka.';
        }

        return null;
    }

    public static function validateEmail(string $email): ?string
    {
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Email tidak valid.';
        }

        return null;
    }

    /** @param array<string, mixed> $user */
    private function issueTokensForUser(array $user): array
    {
        $accessToken = $this->jwt->issueAccessToken((int) $user['id'], (string) $user['role']);
        $refreshPlain = bin2hex(random_bytes(32));
        $expiresAt = (new \DateTimeImmutable())->modify('+' . $this->refreshTtlSeconds . ' seconds');

        $this->refreshTokens->store((int) $user['id'], hash('sha256', $refreshPlain), $expiresAt);

        return [
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
            'tokens' => [
                'accessToken' => $accessToken,
                'expiresIn' => $this->jwt->getAccessTtlSeconds(),
                'tokenType' => 'Bearer',
            ],
            'cookie' => $this->buildRefreshCookie($refreshPlain, $expiresAt),
        ];
    }

    private function resolveRefreshToken(Request $request): ?string
    {
        $fromCookie = $request->cookie(self::REFRESH_COOKIE);
        if (is_string($fromCookie) && $fromCookie !== '') {
            return $fromCookie;
        }

        $body = $request->json();
        $fromBody = trim((string) ($body['refreshToken'] ?? ''));

        return $fromBody !== '' ? $fromBody : null;
    }

    private function buildRefreshCookie(string $plainToken, \DateTimeImmutable $expiresAt): string
    {
        $maxAge = max(0, $expiresAt->getTimestamp() - time());
        $secure = (bool) Env::get('APP_SECURE_COOKIES', false);
        $parts = [
            self::REFRESH_COOKIE . '=' . rawurlencode($plainToken),
            'Path=' . self::REFRESH_COOKIE_PATH,
            'HttpOnly',
            'SameSite=Lax',
            'Max-Age=' . $maxAge,
        ];

        if ($secure) {
            $parts[] = 'Secure';
        }

        return implode('; ', $parts);
    }

    private function clearRefreshCookie(): string
    {
        $secure = (bool) Env::get('APP_SECURE_COOKIES', false);
        $parts = [
            self::REFRESH_COOKIE . '=',
            'Path=' . self::REFRESH_COOKIE_PATH,
            'HttpOnly',
            'SameSite=Lax',
            'Max-Age=0',
        ];

        if ($secure) {
            $parts[] = 'Secure';
        }

        return implode('; ', $parts);
    }
}
