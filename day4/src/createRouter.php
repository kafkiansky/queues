<?php

declare(strict_types=1);

namespace Kafkiansky\Day4;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\ErrorHandler;
use Kafkiansky\Day4\Router\RouterBuilder;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @api
 */
function router(
    LoggerInterface $logger = new NullLogger(),
    ErrorHandler $errorHandler = new DefaultErrorHandler(),
): RouterBuilder {
    return RouterBuilder::buildDefault()
        ->withLogger($logger)
        ->withErrorHandler($errorHandler);
}
