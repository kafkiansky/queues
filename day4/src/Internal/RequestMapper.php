<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Internal;

use Amp\Http\Server\Request;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;

/**
 * @internal
 */
final class RequestMapper
{
    public function __construct(
        private readonly TreeMapper $mapper,
    ) {}

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     * @throws \Throwable
     */
    public function map(Request $request, string $class): object
    {
        return $this->mapper->map(
            $class,
            Source::json($request->getBody()->buffer())
                ->camelCaseKeys(),
        );
    }
}
