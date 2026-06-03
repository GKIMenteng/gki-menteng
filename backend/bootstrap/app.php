<?php

declare(strict_types=1);

use GkiMenteng\Config\Env;
use GkiMenteng\Core\Database;
use GkiMenteng\Core\Router;
use GkiMenteng\Middleware\CorsMiddleware;
use GkiMenteng\Middleware\ErrorHandlerMiddleware;
use GkiMenteng\Middleware\JsonResponseMiddleware;

return static function (string $basePath): Router {
    Env::load($basePath);

    $router = new Router();
    $router->setMiddleware([
        new ErrorHandlerMiddleware((bool) Env::get('APP_DEBUG', false)),
        new CorsMiddleware(Env::get('APP_FRONTEND_URL', '*')),
        new JsonResponseMiddleware(),
    ]);

    Database::configure([
        'host' => Env::get('DB_HOST', '127.0.0.1'),
        'port' => (int) Env::get('DB_PORT', 3306),
        'name' => Env::get('DB_NAME', 'gki_menteng'),
        'user' => Env::get('DB_USER', 'root'),
        'pass' => Env::get('DB_PASS', ''),
    ]);

    $registerRoutes = require $basePath . '/routes/api.php';
    $registerRoutes($router);

    return $router;
};
