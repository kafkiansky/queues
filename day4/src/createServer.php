<?php

declare(strict_types=1);

namespace Kafkiansky\Day4;

use Amp\Http\Server\Driver\SocketClientFactory;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\SocketHttpServer;
use Amp\Socket;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @api
 * @param non-empty-string|non-empty-string[] $hosts
 */
function createServer(
    string|array $hosts,
    LoggerInterface $logger = new NullLogger(),
): HttpServer {
    $server = new SocketHttpServer(
        $logger,
        new Socket\ResourceServerSocketFactory(),
        new SocketClientFactory($logger),
    );

    if (!\is_array($hosts)) {
        $hosts = [$hosts];
    }

    foreach ($hosts as $host) {
        $server->expose($host);
    }

    return $server;
}
