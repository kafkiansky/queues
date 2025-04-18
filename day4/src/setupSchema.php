<?php

declare(strict_types=1);

namespace Kafkiansky\Day4;

use Amp\Postgres\PostgresConnectionPool;

/**
 * @api
 */
function setupSchema(PostgresConnectionPool $pool): void
{
    $pool->query(
        <<<'SQL'
            CREATE TABLE IF NOT EXISTS transactions (
                id UUID NOT NULL,
                account_id UUID NOT NULL,
                type TEXT NOT NULL,
                amount BIGINT NOT NULL,
                PRIMARY KEY (id)
            );
            SQL,
    );
}
