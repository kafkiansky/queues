<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Http;

use Amp\ByteStream\ReadableIterableStream;
use Amp\Http\HttpStatus;
use Amp\Http\Server\Response;

/**
 * @api
 * @param HttpStatus::* $status
 */
function jsonResponse(int $status = HttpStatus::OK, mixed $data = []): Response
{
    return new Response(
        status: $status,
        headers: ['content-type' => 'application/json'],
        body: json_encode($data) ?: '{}',
    );
}

/**
 * @api
 * @param iterable<int, string> $stream
 * @param HttpStatus::* $status
 */
function streamResponse(iterable $stream, int $status = HttpStatus::OK): Response
{
    return new Response(
        status: $status,
        headers: ['content-type' => 'application/json'],
        body: new ReadableIterableStream($stream),
    );
}
