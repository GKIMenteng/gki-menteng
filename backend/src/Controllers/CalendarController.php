<?php

declare(strict_types=1);

namespace GkiMenteng\Controllers;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;
use GkiMenteng\Repositories\CalendarRepository;

final class CalendarController extends Controller
{
    public function __construct(
        private readonly CalendarRepository $repository = new CalendarRepository(),
    ) {
    }

    public function events(Request $request, array $params = []): Response
    {
        $year = $request->query('year');
        $month = $request->query('month');

        return $this->success([
            'events' => $this->repository->getEvents(
                $year !== null ? (int) $year : null,
                $month !== null ? (int) $month : null,
            ),
        ]);
    }

    public function store(Request $request, array $params = []): Response
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof Response) {
            return $user;
        }

        $data = $this->validateEvent($request->json());
        if ($data instanceof Response) {
            return $data;
        }

        $event = $this->repository->create($data);

        return $this->success(['event' => $event], 201);
    }

    public function update(Request $request, array $params = []): Response
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof Response) {
            return $user;
        }

        $id = (int) ($params['id'] ?? 0);
        if ($id <= 0) {
            return Response::error('ID kegiatan tidak valid.', 400);
        }

        $data = $this->validateEvent($request->json());
        if ($data instanceof Response) {
            return $data;
        }

        $event = $this->repository->update($id, $data);
        if ($event === null) {
            return Response::error('Kegiatan tidak ditemukan.', 404);
        }

        return $this->success(['event' => $event]);
    }

    public function destroy(Request $request, array $params = []): Response
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof Response) {
            return $user;
        }

        $id = (int) ($params['id'] ?? 0);
        if ($id <= 0) {
            return Response::error('ID kegiatan tidak valid.', 400);
        }

        if (!$this->repository->delete($id)) {
            return Response::error('Kegiatan tidak ditemukan.', 404);
        }

        return $this->success(['deleted' => true]);
    }

    /** @param array<string, mixed> $body */
    private function validateEvent(array $body): array|Response
    {
        $title = trim((string) ($body['title'] ?? ''));
        $date = trim((string) ($body['date'] ?? ''));
        $time = trim((string) ($body['time'] ?? ''));
        $endTime = trim((string) ($body['endTime'] ?? ''));
        $location = trim((string) ($body['location'] ?? ''));
        $category = trim((string) ($body['category'] ?? ''));
        $color = trim((string) ($body['color'] ?? ''));
        $description = trim((string) ($body['description'] ?? ''));

        $errors = [];
        if ($title === '') {
            $errors['title'] = 'Judul wajib diisi.';
        }
        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors['date'] = 'Tanggal tidak valid.';
        }
        if ($time === '') {
            $errors['time'] = 'Waktu mulai wajib diisi.';
        }
        if ($endTime === '') {
            $errors['endTime'] = 'Waktu selesai wajib diisi.';
        }
        if ($location === '') {
            $errors['location'] = 'Lokasi wajib diisi.';
        }
        if ($category === '') {
            $errors['category'] = 'Kategori wajib dipilih.';
        }
        if ($color === '') {
            $errors['color'] = 'Warna wajib diisi.';
        }

        if ($errors !== []) {
            return Response::error('Data kegiatan tidak lengkap.', 422, $errors);
        }

        return [
            'title' => $title,
            'date' => $date,
            'time' => $time,
            'endTime' => $endTime,
            'location' => $location,
            'description' => $description,
            'category' => $category,
            'color' => $color,
        ];
    }
}
