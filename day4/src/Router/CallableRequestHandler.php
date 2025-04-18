<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Router;

use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;

/**
 * @api
 */
final class CallableRequestHandler implements RequestHandler
{
    /**
     * @param callable(Request): Response $handler
     */
    public function __construct(
        private readonly mixed $handler,
    ) {}

    public function handleRequest(Request $request): Response
    {
        return ($this->handler)($request);
    }
}
