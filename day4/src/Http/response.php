<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Http;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Response;

/**
 * @api
 * @param HttpStatus::* $status
 * @param array<non-empty-string, mixed> $data
 */
function jsonResponse(int $status = HttpStatus::OK, array $data = []): Response
{
    return new Response(
        status: $status,
        headers: ['content-type' => 'application/json'],
        body: json_encode($data) ?: '{}',
    );
}
