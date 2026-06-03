<?php

declare(strict_types=1);

namespace GkiMenteng\Repositories;

use GkiMenteng\Core\Database;

final class DashboardRepository
{
    public function getNews(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT id, title, content, date, image, category
             FROM news
             ORDER BY date DESC, id DESC',
        );

        return array_map([$this, 'mapNews'], $stmt->fetchAll() ?: []);
    }

    public function getDailyReflection(): ?array
    {
        $stmt = Database::pdo()->query(
            'SELECT reflection_date AS date, verse, content, author, theme
             FROM daily_reflections
             ORDER BY reflection_date DESC
             LIMIT 1',
        );

        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getUpcomingEvents(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT id, title, event_date AS date, time, end_time, location, category
             FROM calendar_events
             WHERE event_date >= CURDATE()
               AND event_date <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
             ORDER BY event_date ASC, time ASC',
        );

        return array_map(static fn (array $row): array => [
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'date' => $row['date'],
            'time' => $row['time'] . ' - ' . $row['end_time'],
            'location' => $row['location'],
            'type' => $row['category'],
        ], $stmt->fetchAll() ?: []);
    }

    public function getStats(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT members, ministries, volunteers, events FROM site_stats WHERE id = 1 LIMIT 1',
        );

        $row = $stmt->fetch();

        return [
            'members' => (int) ($row['members'] ?? 0),
            'ministries' => (int) ($row['ministries'] ?? 0),
            'volunteers' => (int) ($row['volunteers'] ?? 0),
            'events' => (int) ($row['events'] ?? 0),
        ];
    }

    private function mapNews(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'date' => $row['date'],
            'image' => $row['image'],
            'category' => $row['category'],
        ];
    }
}
