<?php

declare(strict_types=1);

namespace Kafkiansky\Day4;

use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\Cache\FileWatchingCache;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use Ramsey\Uuid\Uuid;

/**
 * @api
 * @param ?non-empty-string $cachePath
 */
function createMapper(?string $cachePath = null, bool $inDev = false): TreeMapper
{
    $builder = (new MapperBuilder())
        ->registerConstructor(
            Uuid::fromString(...),
        )
        ->allowSuperfluousKeys()
        ->allowPermissiveTypes();

    if ($cachePath !== null) {
        $cache = new FileSystemCache($cachePath);

        if ($inDev) {
            $cache = new FileWatchingCache($cache);
        }

        $builder = $builder->withCache($cache);
    }

    return $builder->mapper();
}
