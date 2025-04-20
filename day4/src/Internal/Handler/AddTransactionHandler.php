<?php

declare(strict_types=1);

namespace Kafkiansky\Day4\Internal\Handler;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Postgres\PostgresConnection;
use Amp\Postgres\PostgresQueryError;
use Kafkiansky\Day4\Http;
use Kafkiansky\Day4\Internal\RequestMapper;
use Kafkiansky\Day4\Sql;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class AddTransactionHandler
{
    public function __construct(
        private readonly PostgresConnection $connection,
        private readonly RequestMapper $mapper,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(Request $request): Response
    {
        try {
            $req = $this->mapper->map($request, AddTransactionRequest::class);
        } catch (\Throwable $e) {
            $this->logger->warning('Invalid request received: {exception}.', [
                'exception' => $e,
            ]);

            return Http\jsonResponse(HttpStatus::BAD_REQUEST);
        }

        $this->logger->info('Transaction {transactionId} received.', [
            'transactionId' => $req->transactionId->toString(),
        ]);

        $query = Sql\insert('transactions')
            ->columns('id', 'account_id', 'amount', 'type')
            ->values(
                $req->transactionId->toString(),
                $req->accountId->toString(),
                $req->amount,
                $req->type->value,
            )
            ->compile();

        try {
            $this->connection->execute($query->sql(), $query->params());
        } catch (PostgresQueryError $e) {
            if (Sql\duplicated($e)) {
                return Http\jsonResponse(
                    HttpStatus::CONFLICT,
                    ['status' => 'duplicated'],
                );
            }

            throw $e;
        } catch (\Throwable $e) {
            $this->logger->error('Add transaction {transactionId} error {exception}.', [
                'transactionId' => $req->transactionId,
                'exception' => $e,
            ]);

            return Http\jsonResponse(
                HttpStatus::INTERNAL_SERVER_ERROR,
            );
        }

        return Http\jsonResponse(
            HttpStatus::OK,
            ['status' => 'ok'],
        );
    }
}
