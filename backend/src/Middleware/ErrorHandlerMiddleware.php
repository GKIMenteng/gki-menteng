<?php

declare(strict_types=1);

namespace GkiMenteng\Middleware;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;
use PDOException;
use Throwable;

final class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly bool $debug,
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            $status = $e instanceof PDOException ? 503 : 500;
            $message = $this->debug ? $e->getMessage() : 'Internal server error.';

            $payload = [
                'success' => false,
                'message' => $message,
            ];

            if ($this->debug) {
                $payload['trace'] = $e->getTraceAsString();
            }

            return Response::json($payload, $status);
        }
    }
}
