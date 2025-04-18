<?php

declare(strict_types=1);

use function Amp\trapSignal;

/**
 * @api
 * @param (callable(int): void)|list<callable(int): void> $hooks
 * @param non-empty-list<int> $signals
 */
function onShutdown(
    callable|array $hooks,
    array $signals = [\SIGHUP, \SIGINT, \SIGQUIT, \SIGTERM],
): void {
    if (is_callable($hooks)) {
        $hooks = [$hooks];
    }

    $signal = trapSignal($signals);

    foreach ($hooks as $hook) {
        $hook($signal);
    }
}
