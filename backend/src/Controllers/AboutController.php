<?php

declare(strict_types=1);

namespace GkiMenteng\Controllers;

use GkiMenteng\Core\Request;
use GkiMenteng\Core\Response;
use GkiMenteng\Repositories\AboutRepository;

final class AboutController extends Controller
{
    public function __construct(
        private readonly AboutRepository $repository = new AboutRepository(),
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        return $this->success([
            'churchProfile' => $this->repository->getChurchProfile(),
            'pastoralTeam' => $this->repository->getPastoralTeam(),
            'churchActivities' => $this->repository->getChurchActivities(),
        ]);
    }
}
