<?php

declare(strict_types=1);

namespace Kafkiansky\Day4;

use CuyZ\Valinor\Mapper\TreeMapper;
use Kafkiansky\Day4\ClickHouse\Client;

/**
 * @api
 * @param non-empty-string $host
 * @param non-empty-string $user
 */
function createClickHouse(
    TreeMapper $mapper,
    string $host,
    string $user,
    ?string $password = null,
): Client {
    return new Client(
        $mapper,
        $host,
        $user,
        $password,
    );
}
