<?php

declare(strict_types=1);

namespace GkiMenteng\Middleware;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;

final class CorsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $allowedOrigin,
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        if ($request->method === 'OPTIONS') {
            return $this->withCorsHeaders(Response::json(['success' => true], 204));
        }

        return $this->withCorsHeaders($next($request));
    }

    private function withCorsHeaders(Response $response): Response
    {
        $headers = [
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ];

        if ($this->allowedOrigin !== '*') {
            $headers['Access-Control-Allow-Origin'] = $this->allowedOrigin;
            $headers['Access-Control-Allow-Credentials'] = 'true';
        } else {
            $headers['Access-Control-Allow-Origin'] = '*';
        }

        return new Response(
            $response->getStatus(),
            $response->getBody(),
            array_merge($response->getHeaders(), $headers),
        );
    }
}
