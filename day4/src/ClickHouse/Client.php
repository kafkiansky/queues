<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\ClickHouse;

use Amp\Http\Client\DelegateHttpClient;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Interceptor\ModifyRequest;
use Amp\Http\Client\Request;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use Psl\Type;

/**
 * @api
 */
final class Client
{
    /** @var non-empty-string */
    private readonly string $host;

    private readonly DelegateHttpClient $clickhouse;

    /**
     * @param non-empty-string $host
     * @param non-empty-string $user
     */
    public function __construct(
        private readonly TreeMapper $mapper,
        string $host,
        string $user,
        ?string $password = null,
    ) {
        $this->host = Type\non_empty_string()->coerce(trim($host, '/'));
        $this->clickhouse = (new HttpClientBuilder())
            ->intercept(new ModifyRequest(static function (Request $request) use ($user, $password): Request {
                $request->setHeaders([
                    'X-ClickHouse-User' => $user,
                    'X-ClickHouse-Key' => $password ?: '',
                ]);

                return $request;
            }))
            ->build();
    }

    /**
     * @template T of object
     * @param non-empty-string $sql
     * @param class-string<T> $type
     * @return iterable<T>
     * @throws \Throwable
     */
    public function selectRows(string $sql, string $type): iterable
    {
        $response = $this->clickhouse->request(
            new Request(\sprintf('%s/?default_format=JSON&query=%s', $this->host, urlencode($sql))),
        );

        /** @var array{data?: list<array<string, mixed>>} $result */
        $result = json_decode($response->getBody()->buffer(), true);

        foreach ($result['data'] ?? [] as $item) {
            yield $this->mapper->map($type, Source::array($item)->camelCaseKeys());
        }
    }
}
