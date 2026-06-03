<?php

declare(strict_types=1);

namespace GkiMenteng\Repositories;

use GkiMenteng\Core\Database;

final class AboutRepository
{
    public function getChurchProfile(): ?array
    {
        $stmt = Database::pdo()->query(
            'SELECT name, full_name AS fullName, established, address, phone, email,
                    website, denomination, vision, mission, history, description
             FROM church_profile
             WHERE id = 1
             LIMIT 1',
        );

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $row['mission'] = json_decode($row['mission'] ?? '[]', true) ?: [];

        return $row;
    }

    public function getPastoralTeam(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT name, position, education, email, image
             FROM pastoral_team
             ORDER BY sort_order ASC, id ASC',
        );

        return $stmt->fetchAll() ?: [];
    }

    public function getChurchActivities(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT name, time, location
             FROM church_activities
             ORDER BY sort_order ASC, id ASC',
        );

        return $stmt->fetchAll() ?: [];
    }
}
