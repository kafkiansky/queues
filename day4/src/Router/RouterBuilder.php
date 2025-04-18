<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Router;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Router;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @api
 */
final class RouterBuilder
{
    public const HTTP_METHOD_GET = 'GET';
    public const HTTP_METHOD_POST = 'POST';

    /** @var list<array{RequestHandler, non-empty-string, self::HTTP_METHOD_*}> */
    private array $routes = [];

    private function __construct(
        private readonly LoggerInterface $logger = new NullLogger(),
        private readonly ErrorHandler $errorHandler = new DefaultErrorHandler(),
    ) {}

    public static function buildDefault(): self
    {
        return new self();
    }

    public function withLogger(LoggerInterface $logger): self
    {
        $server = new self(
            $logger,
            $this->errorHandler,
        );

        $server->routes = $this->routes;

        return $server;
    }

    public function withErrorHandler(ErrorHandler $errorHandler): self
    {
        $server = new self(
            $this->logger,
            $errorHandler,
        );

        $server->routes = $this->routes;

        return $server;
    }

    /**
     * @param non-empty-string $path
     * @param self::HTTP_METHOD_* $method
     */
    public function route(
        string $path,
        RequestHandler $handler,
        string $method = self::HTTP_METHOD_GET,
    ): self {
        $server = new self(
            $this->logger,
            $this->errorHandler,
        );

        $server->routes[] = [$handler, $path, $method];

        return $server;
    }

    public function build(HttpServer $server): Router
    {
        $router = new Router(
            $server,
            $this->logger,
            $this->errorHandler,
        );

        foreach ($this->routes as [$handler, $path, $method]) {
            $router->addRoute($method, $path, $handler);
        }

        return $router;
    }
}
