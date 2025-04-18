<?php

declare(strict_types=1);

namespace Kafkiansky\Day4;

use Psl\Type;

/**
 * @api
 * @template T = mixed
 * @param non-empty-string $name
 * @param ?Type\TypeInterface<T> $type
 * @return ($type is not null ? iterable<T> : iterable<non-empty-string>)
 */
function parseEnvList(string $name, ?Type\TypeInterface $type = null): iterable
{
    $type ??= Type\non_empty_string();

    foreach (explode(',', parseEnvString($name) ?: '') as $value) {
        yield $type->coerce($value);
    }
}

/**
 * @api
 * @param non-empty-string $name
 * @param ?non-empty-string $default
 * @return ($default is not null ? non-empty-string : ?string)
 */
function parseEnvString(string $name, ?string $default = null): ?string
{
    return parseEnv($name, Type\non_empty_string(), $default);
}

/**
 * @api
 * @template T = mixed
 * @param non-empty-string $name
 * @param Type\TypeInterface<T> $type
 * @param T $default
 * @return T
 */
function parseEnv(string $name, Type\TypeInterface $type, mixed $default = null): mixed
{
    return $type->coerce($_ENV[$name] ?? $default);
}
