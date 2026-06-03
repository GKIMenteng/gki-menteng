<?php

declare(strict_types=1);

namespace GkiMenteng\Bootstrap;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Router;

final class Application
{
    private function __construct(
        private readonly Router $router,
    ) {
    }

    public static function create(string $basePath): self
    {
        $bootstrap = require $basePath . '/bootstrap/app.php';

        return new self($bootstrap($basePath));
    }

    public function run(): void
    {
        $request = Request::fromGlobals();
        $response = $this->router->dispatch($request);
        $response->send();
    }
}
