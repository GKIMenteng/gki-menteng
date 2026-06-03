<?php

declare(strict_types=1);

namespace GkiMenteng\Controllers;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;
use GkiMenteng\Services\AuthService;

abstract class Controller
{
    protected function success(mixed $data, int $status = 200): Response
    {
        return Response::json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /** @return array<string, mixed>|Response */
    protected function requireAuthenticatedUser(
        Request $request,
        AuthService $auth = new AuthService(),
    ): array|Response {
        $user = $auth->userFromRequest($request);
        if ($user === null) {
            return Response::error('Anda harus masuk untuk melakukan tindakan ini.', 401);
        }

        return $user;
    }
}
