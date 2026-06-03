<?php

declare(strict_types=1);

use GkiMenteng\Controllers\AboutController;
use GkiMenteng\Controllers\AuthController;
use GkiMenteng\Controllers\CalendarController;
use GkiMenteng\Controllers\DashboardController;
use GkiMenteng\Controllers\HealthController;
use GkiMenteng\Controllers\VolunteersController;
use GkiMenteng\Core\Router;

return static function (Router $router): void {
    $health = new HealthController();
    $auth = new AuthController();
    $dashboard = new DashboardController();
    $calendar = new CalendarController();
    $volunteers = new VolunteersController();
    $about = new AboutController();

    $router->get('/api/health', $health->index(...));
    $router->add(['POST'], '/api/auth/register', $auth->register(...));
    $router->add(['POST'], '/api/auth/login', $auth->login(...));
    $router->add(['POST'], '/api/auth/refresh', $auth->refresh(...));
    $router->add(['POST'], '/api/auth/logout', $auth->logout(...));
    $router->get('/api/auth/me', $auth->me(...));
    $router->get('/api/dashboard', $dashboard->index(...));
    $router->get('/api/calendar/events', $calendar->events(...));
    $router->add(['POST'], '/api/calendar/events', $calendar->store(...));
    $router->add(['PUT'], '/api/calendar/events/{id}', $calendar->update(...));
    $router->add(['DELETE'], '/api/calendar/events/{id}', $calendar->destroy(...));
    $router->get('/api/volunteers', $volunteers->index(...));
    $router->add(['POST'], '/api/volunteers', $volunteers->store(...));
    $router->add(['PUT'], '/api/volunteers/{id}', $volunteers->update(...));
    $router->add(['DELETE'], '/api/volunteers/{id}', $volunteers->destroy(...));
    $router->get('/api/about', $about->index(...));
};
