<?php

declare(strict_types=1);

namespace GkiMenteng\Repositories;

use GkiMenteng\Core\Database;

final class VolunteersRepository
{
    public function getVolunteers(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT id, name, role, ministry, phone, email, experience, image
             FROM volunteers
             ORDER BY id ASC',
        );

        return array_map($this->mapVolunteer(...), $stmt->fetchAll() ?: []);
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT id, name, role, ministry, phone, email, experience, image
             FROM volunteers WHERE id = :id',
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->mapVolunteer($row) : null;
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): array
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO volunteers (name, role, ministry, schedule, status, phone, email, experience, image)
             VALUES (:name, :role, :ministry, :schedule, :status, :phone, :email, :experience, :image)',
        );
        $stmt->execute([
            'name' => $data['name'],
            'role' => $data['role'],
            'ministry' => $data['ministry'],
            'schedule' => '-',
            'status' => 'Available',
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'experience' => $data['experience'] ?? null,
            'image' => $data['image'] ?? null,
        ]);

        $id = (int) Database::pdo()->lastInsertId();

        return $this->findById($id) ?? [];
    }

    /** @param array<string, mixed> $data */
    public function update(int $id, array $data): ?array
    {
        if ($this->findById($id) === null) {
            return null;
        }

        $stmt = Database::pdo()->prepare(
            'UPDATE volunteers
             SET name = :name, role = :role, ministry = :ministry,
                 phone = :phone, email = :email, experience = :experience, image = :image
             WHERE id = :id',
        );
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'role' => $data['role'],
            'ministry' => $data['ministry'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'experience' => $data['experience'] ?? null,
            'image' => $data['image'] ?? null,
        ]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::pdo()->prepare('DELETE FROM volunteers WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function getMinistries(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT m.name, COUNT(v.id) AS count
             FROM ministries m
             LEFT JOIN volunteers v ON v.ministry = m.name
             GROUP BY m.name
             ORDER BY m.name ASC',
        );

        return array_map(static fn (array $row): array => [
            'name' => $row['name'],
            'count' => (int) $row['count'],
        ], $stmt->fetchAll() ?: []);
    }

    /** @param array<string, mixed> $row */
    private function mapVolunteer(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'name' => (string) $row['name'],
            'role' => (string) $row['role'],
            'ministry' => (string) $row['ministry'],
            'phone' => $row['phone'] !== null ? (string) $row['phone'] : '',
            'email' => $row['email'] !== null ? (string) $row['email'] : '',
            'experience' => $row['experience'] !== null ? (string) $row['experience'] : '',
            'image' => $row['image'] !== null ? (string) $row['image'] : '',
        ];
    }

    public function getSundayServiceSchedule(): ?array
    {
        $stmt = Database::pdo()->query(
            'SELECT id, schedule_date AS date FROM sunday_schedules
             ORDER BY schedule_date DESC
             LIMIT 1',
        );

        $schedule = $stmt->fetch();
        if (!$schedule) {
            return null;
        }

        $servicesStmt = Database::pdo()->prepare(
            'SELECT time_slot AS time, preacher, worship_leader AS worshipLeader,
                    musicians, hospitality, multimedia, sunday_school AS sundaySchool
             FROM sunday_services
             WHERE schedule_id = :schedule_id
             ORDER BY id ASC',
        );
        $servicesStmt->execute(['schedule_id' => $schedule['id']]);
        $services = $servicesStmt->fetchAll() ?: [];

        return [
            'date' => $schedule['date'],
            'services' => array_map([$this, 'mapService'], $services),
        ];
    }

    private function mapService(array $row): array
    {
        return [
            'time' => $row['time'],
            'preacher' => $row['preacher'],
            'worshipLeader' => $row['worshipLeader'],
            'musicians' => $this->decodeJsonList($row['musicians']),
            'hospitality' => $this->decodeJsonList($row['hospitality']),
            'multimedia' => $row['multimedia'],
            'sundaySchool' => $this->decodeJsonList($row['sundaySchool']),
        ];
    }

    private function decodeJsonList(?string $json): array
    {
        if ($json === null || $json === '') {
            return [];
        }

        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }
}
