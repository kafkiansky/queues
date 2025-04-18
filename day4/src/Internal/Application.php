<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Internal;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Router;
use Amp\Postgres\PostgresConnectionPool;
use Kafkiansky\Day4\Internal\Handler\AddTransactionHandler;
use Kafkiansky\Day4\Router\CallableRequestHandler;
use Kafkiansky\Day4\Router\RouterBuilder;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use function Kafkiansky\Day4\createLogger;
use function Kafkiansky\Day4\createMapper;
use function Kafkiansky\Day4\createPostgresPool;
use function Kafkiansky\Day4\createServer;
use function Kafkiansky\Day4\parseEnvBool;
use function Kafkiansky\Day4\parseEnvList;
use function Kafkiansky\Day4\parseEnvString;
use function Kafkiansky\Day4\router;
use function Kafkiansky\Day4\setupSchema;

/**
 * @internal
 */
final class Application
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PostgresConnectionPool $pool,
        private readonly HttpServer $server,
        private readonly Router $router,
    ) {}

    public static function buildFromEnv(): self
    {
        $logger = createLogger(
            logLevel: parseEnvString('LOG_LEVEL', LogLevel::DEBUG),
        );

        $pool = createPostgresPool(
            dsn: parseEnvString('POSTGRES_DSN') ?: throw new \InvalidArgumentException('POSTGRES_DSN environment is empty.'),
        );

        setupSchema($pool);

        $server = createServer(
            hosts: [...parseEnvList('SERVER_HOST')],
            logger: $logger,
        );

        $mapper = createMapper(
            parseEnvString('CACHE_DIR'),
            parseEnvBool('APP_DEBUG'),
        );

        $router = router($logger)
            ->route('/transactions/add', new CallableRequestHandler(new AddTransactionHandler($pool, new RequestMapper($mapper), $logger)), RouterBuilder::HTTP_METHOD_POST)
            ->build($server);

        return new self(
            $logger,
            $pool,
            $server,
            $router,
        );
    }

    public function start(ErrorHandler $errorHandler = new DefaultErrorHandler()): void
    {
        $this->server->start($this->router, $errorHandler);
    }

    public function stop(): void
    {
        $this->logger->info('Graceful shutdown received.');

        $this->server->stop();
        $this->pool->close();
    }
}
