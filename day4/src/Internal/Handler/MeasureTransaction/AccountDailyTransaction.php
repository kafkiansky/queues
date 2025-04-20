<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Internal\Handler\MeasureTransaction;

use Ramsey\Uuid\UuidInterface;

/**
 * @internal
 */
final class AccountDailyTransaction implements \JsonSerializable
{
    public readonly int $count;

    public readonly int $amount;

    /**
     * @param numeric-string $count
     * @param numeric-string $amount
     */
    public function __construct(
        public readonly UuidInterface $accountId,
        public readonly \DateTimeImmutable $date,
        string $count,
        string $amount,
    ) {
        $this->count = (int) $count;
        $this->amount = (int) $amount;
    }

    /**
     * @return array{
     *     accountId: non-empty-string,
     *     date: non-empty-string,
     *     count: int,
     *     amount: int,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'accountId' => $this->accountId->toString(),
            'date' => $this->date->format('Y-m-d'),
            'count' => $this->count,
            'amount' => $this->amount,
        ];
    }
}
