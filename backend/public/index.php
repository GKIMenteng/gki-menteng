<?php

declare(strict_types=1);

use GkiMenteng\Bootstrap\Application;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = Application::create(dirname(__DIR__));
$app->run();
