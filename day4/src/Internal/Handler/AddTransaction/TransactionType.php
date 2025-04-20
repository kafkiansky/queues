<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Internal\Handler\AddTransaction;

/**
 * @internal
 */
enum TransactionType: string
{
    case Credit = 'CREDIT';
    case Debit = 'DEBIT';
}
