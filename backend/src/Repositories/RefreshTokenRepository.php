<?php

declare(strict_types=1);

namespace GkiMenteng\Repositories;

use GkiMenteng\Core\Database;

final class RefreshTokenRepository
{
    public function store(int $userId, string $tokenHash, \DateTimeImmutable $expiresAt): void
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO auth_refresh_tokens (user_id, token_hash, expires_at)
             VALUES (:user_id, :token_hash, :expires_at)',
        );
        $stmt->execute([
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findValid(string $tokenHash): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT id, user_id, expires_at
             FROM auth_refresh_tokens
             WHERE token_hash = :token_hash
               AND revoked_at IS NULL
               AND expires_at > NOW()
             LIMIT 1',
        );
        $stmt->execute(['token_hash' => $tokenHash]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'userId' => (int) $row['user_id'],
        ];
    }

    public function revoke(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE auth_refresh_tokens SET revoked_at = NOW() WHERE id = :id',
        );
        $stmt->execute(['id' => $id]);
    }

    public function revokeAllForUser(int $userId): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE auth_refresh_tokens SET revoked_at = NOW()
             WHERE user_id = :user_id AND revoked_at IS NULL',
        );
        $stmt->execute(['user_id' => $userId]);
    }
}
