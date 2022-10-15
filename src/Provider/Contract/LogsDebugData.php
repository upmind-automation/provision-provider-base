<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Contract;

use Psr\Log\LoggerInterface;
use Upmind\ProvisionBase\Provider\Storage\Storage;

/**
 * Marker interface used to indicate Providers which need a logger instance.
 */
interface LogsDebugData
{
    /**
     * Set the Logger instance to facilitate debug logging.
     */
    public function setLogger(LoggerInterface $logger): void;

    /**
     * Get the Logger instance for debug logging.
     */
    public function getLogger(): LoggerInterface;
}
