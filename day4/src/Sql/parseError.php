<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Sql;

use Amp\Postgres\PostgresQueryError;

/**
 * @api
 */
function duplicated(PostgresQueryError $error): bool
{
    return str_contains($error->getMessage(), 'duplicate key value violates unique constraint');
}
