<?php

declare(strict_types=1);

namespace GkiMenteng\Controllers;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;
use GkiMenteng\Repositories\VolunteersRepository;

final class VolunteersController extends Controller
{
    public function __construct(
        private readonly VolunteersRepository $repository = new VolunteersRepository(),
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        return $this->success([
            'volunteers' => $this->repository->getVolunteers(),
            'ministries' => $this->repository->getMinistries(),
            'sundayServiceSchedule' => $this->repository->getSundayServiceSchedule(),
        ]);
    }

    public function store(Request $request, array $params = []): Response
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof Response) {
            return $user;
        }

        $data = $this->validateVolunteer($request->json());
        if ($data instanceof Response) {
            return $data;
        }

        $volunteer = $this->repository->create($data);

        return $this->success(['volunteer' => $volunteer], 201);
    }

    public function update(Request $request, array $params = []): Response
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof Response) {
            return $user;
        }

        $id = (int) ($params['id'] ?? 0);
        if ($id <= 0) {
            return Response::error('ID volunteer tidak valid.', 400);
        }

        $data = $this->validateVolunteer($request->json());
        if ($data instanceof Response) {
            return $data;
        }

        $volunteer = $this->repository->update($id, $data);
        if ($volunteer === null) {
            return Response::error('Volunteer tidak ditemukan.', 404);
        }

        return $this->success(['volunteer' => $volunteer]);
    }

    public function destroy(Request $request, array $params = []): Response
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof Response) {
            return $user;
        }

        $id = (int) ($params['id'] ?? 0);
        if ($id <= 0) {
            return Response::error('ID volunteer tidak valid.', 400);
        }

        if (!$this->repository->delete($id)) {
            return Response::error('Volunteer tidak ditemukan.', 404);
        }

        return $this->success(['deleted' => true]);
    }

    /** @param array<string, mixed> $body */
    private function validateVolunteer(array $body): array|Response
    {
        $name = trim((string) ($body['name'] ?? ''));
        $role = trim((string) ($body['role'] ?? ''));
        $ministry = trim((string) ($body['ministry'] ?? ''));
        $phone = trim((string) ($body['phone'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));
        $experience = trim((string) ($body['experience'] ?? ''));
        $image = trim((string) ($body['image'] ?? ''));

        $errors = [];
        if ($name === '') {
            $errors['name'] = 'Nama wajib diisi.';
        }
        if ($role === '') {
            $errors['role'] = 'Peran wajib diisi.';
        }
        if ($ministry === '') {
            $errors['ministry'] = 'Pelayanan wajib dipilih.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email tidak valid.';
        }

        if ($errors !== []) {
            return Response::error('Data volunteer tidak lengkap.', 422, $errors);
        }

        return [
            'name' => $name,
            'role' => $role,
            'ministry' => $ministry,
            'phone' => $phone,
            'email' => $email,
            'experience' => $experience,
            'image' => $image,
        ];
    }
}
