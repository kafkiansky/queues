<?php

declare(strict_types=1);

namespace Kafkiansky\Day4;

use Amp\Postgres\PostgresConfig;
use Amp\Postgres\PostgresConnectionPool;

/**
 * @api
 * @param non-empty-string $dsn
 */
function createPostgresPool(string $dsn): PostgresConnectionPool
{
    $pool = new PostgresConnectionPool(
        PostgresConfig::fromString($dsn),
    );

    $pool->query('SELECT 1');

    return $pool;
}
