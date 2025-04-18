<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Internal\Handler;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Postgres\PostgresConnection;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class AddTransactionHandler
{
    public function __construct(
        private readonly PostgresConnection $sql,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(Request $request): Response
    {
        return new Response(
            status: HttpStatus::OK,
            headers: ['content-type' => 'application/json'],
            body: '{}',
        );
    }
}
