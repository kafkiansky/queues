<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Kafkiansky\Day4\Internal\Application;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__ . '/../.env');

$app = Application::buildFromEnv();
$app->start();

onShutdown($app->stop(...));
