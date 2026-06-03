<?php

declare(strict_types=1);

namespace GkiMenteng\Middleware;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;

final class JsonResponseMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $response = $next($request);

        $headers = $response->getHeaders();
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json; charset=utf-8';
        }

        return new Response($response->getStatus(), $response->getBody(), $headers);
    }
}
