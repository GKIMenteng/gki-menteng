<?php

declare(strict_types=1);

namespace GkiMenteng\Repositories;

use GkiMenteng\Core\Database;

final class CalendarRepository
{
    public function getEvents(?int $year = null, ?int $month = null): array
    {
        $sql = 'SELECT id, title, event_date AS date, time, end_time AS endTime,
                       location, description, category, color
                FROM calendar_events';
        $params = [];

        if ($year !== null && $month !== null) {
            $sql .= ' WHERE YEAR(event_date) = :year AND MONTH(event_date) = :month';
            $params['year'] = $year;
            $params['month'] = $month;
        }

        $sql .= ' ORDER BY event_date ASC, time ASC';

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);

        return array_map($this->mapRow(...), $stmt->fetchAll() ?: []);
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT id, title, event_date AS date, time, end_time AS endTime,
                    location, description, category, color
             FROM calendar_events WHERE id = :id',
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->mapRow($row) : null;
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): array
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO calendar_events (title, event_date, time, end_time, location, description, category, color)
             VALUES (:title, :event_date, :time, :end_time, :location, :description, :category, :color)',
        );
        $stmt->execute([
            'title' => $data['title'],
            'event_date' => $data['date'],
            'time' => $data['time'],
            'end_time' => $data['endTime'],
            'location' => $data['location'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'],
            'color' => $data['color'],
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
            'UPDATE calendar_events
             SET title = :title, event_date = :event_date, time = :time, end_time = :end_time,
                 location = :location, description = :description, category = :category, color = :color
             WHERE id = :id',
        );
        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'event_date' => $data['date'],
            'time' => $data['time'],
            'end_time' => $data['endTime'],
            'location' => $data['location'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'],
            'color' => $data['color'],
        ]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::pdo()->prepare('DELETE FROM calendar_events WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /** @param array<string, mixed> $row */
    private function mapRow(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'title' => (string) $row['title'],
            'date' => (string) $row['date'],
            'time' => (string) $row['time'],
            'endTime' => (string) $row['endTime'],
            'location' => (string) $row['location'],
            'description' => (string) ($row['description'] ?? ''),
            'category' => (string) $row['category'],
            'color' => (string) $row['color'],
        ];
    }
}
