<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Sql;

use Latitude\QueryBuilder\Engine\PostgresEngine;
use Latitude\QueryBuilder\Query;
use Latitude\QueryBuilder\QueryFactory;

/**
 * @api
 * @param non-empty-string $table
 */
function insert(string $table): Query\InsertQuery
{
    /** @var ?QueryFactory $qb */
    static $qb = null;
    $qb ??= createQueryBuilder();

    return $qb->insert($table);
}

/**
 * @api
 */
function createQueryBuilder(): QueryFactory
{
    return new QueryFactory(new PostgresEngine());
}
