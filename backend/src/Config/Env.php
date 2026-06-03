<?php

declare(strict_types=1);

namespace GkiMenteng\Config;

use Dotenv\Dotenv;

final class Env
{
    private static bool $loaded = false;

  /** @var array<string, string|null> */
    private static array $values = [];

    public static function load(string $basePath): void
    {
        if (self::$loaded) {
            return;
        }

        if (is_file($basePath . '/.env')) {
            $dotenv = Dotenv::createImmutable($basePath);
            $dotenv->safeLoad();
        }

        self::$values = $_ENV;
        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$values[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
