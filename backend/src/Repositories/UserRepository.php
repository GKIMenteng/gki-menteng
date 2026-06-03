<?php

declare(strict_types=1);

namespace GkiMenteng\Repositories;

use GkiMenteng\Core\Database;

final class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT id, name, email, password_hash, role, created_at
             FROM users WHERE email = :email LIMIT 1',
        );
        $stmt->execute(['email' => strtolower($email)]);
        $row = $stmt->fetch();

        return $row ? $this->mapUser($row, includePassword: true) : null;
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT id, name, email, role, created_at
             FROM users WHERE id = :id LIMIT 1',
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->mapUser($row) : null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = Database::pdo()->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => strtolower($email)]);

        return (bool) $stmt->fetchColumn();
    }

    /** @return array{id: int, name: string, email: string, role: string, createdAt: string} */
    public function create(string $name, string $email, string $passwordHash, string $role = 'member'): array
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO users (name, email, password_hash, role)
             VALUES (:name, :email, :password_hash, :role)',
        );
        $stmt->execute([
            'name' => $name,
            'email' => strtolower($email),
            'password_hash' => $passwordHash,
            'role' => $role,
        ]);

        $user = $this->findById((int) Database::pdo()->lastInsertId());
        if ($user === null) {
            throw new \RuntimeException('Failed to load user after registration.');
        }

        return $user;
    }

    /** @param array<string, mixed> $row */
    private function mapUser(array $row, bool $includePassword = false): array
    {
        $user = [
            'id' => (int) $row['id'],
            'name' => (string) $row['name'],
            'email' => (string) $row['email'],
            'role' => (string) $row['role'],
            'createdAt' => (string) $row['created_at'],
        ];

        if ($includePassword) {
            $user['passwordHash'] = (string) $row['password_hash'];
        }

        return $user;
    }
}
