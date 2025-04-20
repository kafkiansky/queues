<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Internal\Handler\MeasureTransaction;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Response;
use Kafkiansky\Day4\ClickHouse\Client;
use Kafkiansky\Day4\Http;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class GetDailyTransactionsHandler
{
    public function __construct(
        private readonly Client $clickhouse,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(): Response
    {
        try {
            $transactions = $this->clickhouse->selectRows(
                <<<'SQL'
                    SELECT
                        account_id,
                        date,
                        countMerge(count) AS count,
                        sumMerge(amount) AS amount
                    FROM
                        day4.transactions_daily
                    GROUP BY
                        account_id, date
                    ORDER BY
                        date DESC,
                        count DESC
                    SQL,
                AccountDailyTransaction::class,
            );

            return Http\jsonResponse(
                data: [...$transactions],
            );
        } catch (\Throwable $e) {
            $this->logger->error('Query daily transactions error {exception}.', [
                'exception' => $e,
            ]);

            return Http\jsonResponse(HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }
}
