<?php

declare(strict_types=1);

namespace GkiMenteng\Controllers;

use GkiMenteng\Core\Database;
use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;

final class HealthController extends Controller
{
    public function index(Request $request, array $params = []): Response
    {
        $dbStatus = 'ok';

        try {
            Database::pdo()->query('SELECT 1');
        } catch (\Throwable) {
            $dbStatus = 'error';
        }

        return $this->success([
            'status' => 'ok',
            'database' => $dbStatus,
            'timestamp' => date('c'),
        ]);
    }
}
