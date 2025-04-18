<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Internal\Handler;

use Ramsey\Uuid\UuidInterface;

/**
 * @internal
 */
final class AddTransactionRequest
{
    /**
     * @param positive-int $amount
     */
    public function __construct(
        public readonly UuidInterface $transactionId,
        public readonly UuidInterface $accountId,
        public readonly int $amount,
        public readonly TransactionType $type,
    ) {}
}
