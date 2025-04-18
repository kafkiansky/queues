<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Amp\Http\HttpStatus;
use Psr\Log\LogLevel;
use Symfony\Component\Dotenv\Dotenv;
use function Amp\trapSignal;
use function Kafkiansky\Day4\createServer;
use function Kafkiansky\Day4\parseEnvList;
use function Kafkiansky\Day4\parseEnvString;
use function Kafkiansky\Day4\createLogger;
use function Kafkiansky\Day4\createPostgresPool;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;

(new Dotenv())->bootEnv(__DIR__ . '/../.env');

$logger = createLogger(
    logLevel: parseEnvString('LOG_LEVEL', LogLevel::DEBUG),
);

$pool = createPostgresPool(
    dsn: parseEnvString('POSTGRES_DSN') ?: throw new \InvalidArgumentException('POSTGRES_DSN environment is empty.'),
);

$server = createServer(
    hosts: [...parseEnvList('SERVER_HOST')],
    logger: $logger,
);

$server->start(new class implements RequestHandler {
    public function handleRequest(Request $request): Response
    {
        return new Response(
            status: HttpStatus::OK,
            body: 'Hello, CDC.',
        );
    }
}, new DefaultErrorHandler());

$signal = trapSignal([\SIGHUP, \SIGINT, \SIGQUIT, \SIGTERM]);

$logger->info(sprintf('Received signal %d, stopping HTTP server', $signal));

$server->stop();
