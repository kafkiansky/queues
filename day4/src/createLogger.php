<?php

declare(strict_types=1);

namespace Kafkiansky\Day4;

use Amp\ByteStream;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @api
 * @param non-empty-string $logLevel
 */
function createLogger(string $logLevel = LogLevel::DEBUG): LoggerInterface
{
    $handler = new StreamHandler(ByteStream\getStdout(), parseLogLevel($logLevel));
    $handler->pushProcessor(new PsrLogMessageProcessor());
    $handler->setFormatter(new ConsoleFormatter());

    return new Logger('server', handlers: [$handler]);
}

/** @var array<LogLevel::*, true> */
const logLevels = [
    LogLevel::DEBUG => true,
    LogLevel::INFO => true,
    LogLevel::WARNING => true,
    LogLevel::NOTICE => true,
    LogLevel::ERROR => true,
    LogLevel::CRITICAL => true,
    LogLevel::ALERT => true,
    LogLevel::EMERGENCY => true,
];

/**
 * @internal
 * @param non-empty-string $logLevel
 * @return LogLevel::*
 */
function parseLogLevel(string $logLevel): string
{
    $logLevel = strtolower($logLevel);

    if (!isset(logLevels[$logLevel])) {
        throw new \InvalidArgumentException(
            \sprintf('The log level "%s" is not one of "%s".', $logLevel, implode(', ', array_keys(logLevels))),
        );
    }

    return $logLevel;
}
