<?php

declare(strict_types=1);

namespace GkiMenteng\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    /** @param array{host: string, port: int, name: string, user: string, pass: string} $config */
    public static function configure(array $config): void
    {
        if (self::$connection !== null) {
            return;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['name'],
        );

        try {
            self::$connection = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException('Database connection failed: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    public static function pdo(): PDO
    {
        if (self::$connection === null) {
            throw new PDOException('Database is not configured.');
        }

        return self::$connection;
    }
}
