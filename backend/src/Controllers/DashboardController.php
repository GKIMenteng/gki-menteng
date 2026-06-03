<?php

declare(strict_types=1);

namespace GkiMenteng\Controllers;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;
use GkiMenteng\Repositories\DashboardRepository;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardRepository $repository = new DashboardRepository(),
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        return $this->success([
            'news' => $this->repository->getNews(),
            'dailyReflection' => $this->repository->getDailyReflection(),
            'upcomingEvents' => $this->repository->getUpcomingEvents(),
            'stats' => $this->repository->getStats(),
        ]);
    }
}
