<?php

declare(strict_types=1);

namespace GkiMenteng\Controllers;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;
use GkiMenteng\Services\AuthException;
use GkiMenteng\Services\AuthService;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
    ) {
    }

    public function register(Request $request, array $params = []): Response
    {
        $body = $request->json();
        $errors = $this->validateRegistration($body);
        if ($errors !== []) {
            return Response::error('Data pendaftaran tidak valid.', 422, $errors);
        }

        try {
            $result = $this->auth->register(
                trim((string) $body['name']),
                strtolower(trim((string) $body['email'])),
                (string) $body['password'],
            );
        } catch (AuthException $e) {
            return Response::error($e->getMessage(), $e->getStatusCode(), $e->getErrors() ?: null);
        }

        return $this->authResponse($result, 201);
    }

    public function login(Request $request, array $params = []): Response
    {
        $body = $request->json();
        $email = strtolower(trim((string) ($body['email'] ?? '')));
        $password = (string) ($body['password'] ?? '');

        $errors = [];
        if (AuthService::validateEmail($email) !== null) {
            $errors['email'] = 'Email tidak valid.';
        }
        if ($password === '') {
            $errors['password'] = 'Kata sandi wajib diisi.';
        }

        if ($errors !== []) {
            return Response::error('Data login tidak valid.', 422, $errors);
        }

        try {
            $result = $this->auth->login($email, $password);
        } catch (AuthException $e) {
            return Response::error($e->getMessage(), $e->getStatusCode());
        }

        return $this->authResponse($result);
    }

    public function refresh(Request $request, array $params = []): Response
    {
        try {
            $result = $this->auth->refresh($request);
        } catch (AuthException $e) {
            return Response::error($e->getMessage(), $e->getStatusCode());
        }

        return $this->authResponse($result);
    }

    public function logout(Request $request, array $params = []): Response
    {
        $clearCookie = $this->auth->logout($request);

        return $this->withCookie(
            $this->success(['loggedOut' => true]),
            $clearCookie,
        );
    }

    public function me(Request $request, array $params = []): Response
    {
        $user = $this->auth->userFromRequest($request);
        if ($user === null) {
            return Response::error('Tidak terautentikasi.', 401);
        }

        return $this->success([
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);
    }

    /** @param array<string, mixed> $body */
    private function validateRegistration(array $body): array
    {
        $errors = [];
        $name = trim((string) ($body['name'] ?? ''));
        $email = strtolower(trim((string) ($body['email'] ?? '')));
        $password = (string) ($body['password'] ?? '');
        $confirmation = (string) ($body['passwordConfirmation'] ?? '');

        if ($name === '') {
            $errors['name'] = 'Nama wajib diisi.';
        } elseif (strlen($name) > 255) {
            $errors['name'] = 'Nama terlalu panjang.';
        }

        $emailError = AuthService::validateEmail($email);
        if ($emailError !== null) {
            $errors['email'] = $emailError;
        }

        $passwordError = AuthService::validatePassword($password);
        if ($passwordError !== null) {
            $errors['password'] = $passwordError;
        }

        if ($password !== $confirmation) {
            $errors['passwordConfirmation'] = 'Konfirmasi kata sandi tidak cocok.';
        }

        return $errors;
    }

    /** @param array{user: array, tokens: array, cookie: string|null} $result */
    private function authResponse(array $result, int $status = 200): Response
    {
        return $this->withCookie(
            $this->success([
                'user' => $result['user'],
                'tokens' => $result['tokens'],
            ], $status),
            $result['cookie'],
        );
    }

    private function withCookie(Response $response, ?string $cookie): Response
    {
        if ($cookie === null) {
            return $response;
        }

        $headers = $response->getHeaders();
        $headers['Set-Cookie'] = $cookie;

        return new Response(
            $response->getStatus(),
            $response->getBody(),
            $headers,
        );
    }
}
