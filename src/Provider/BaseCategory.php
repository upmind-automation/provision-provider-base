<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider;

use LogicException;
use Psr\Log\LoggerInterface;
use Throwable;
use Upmind\ProvisionBase\Exception\ProvisionFunctionError;
use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\SystemInfo;

/**
 * The root class all Categories should extend from
 */
abstract class BaseCategory implements Contract\CategoryInterface
{
    /**
     * Debug logger.
     *
     * @var LoggerInterface|null
     */
    protected $log;

    /**
     * System/environment metadata.
     *
     * @var SystemInfo|null
     */
    protected $systemInfo;

    /**
     * Returns the result of a successful provision function.
     *
     * @param string $message A user-friendly success message
     * @param mixed[] $data JSONable data to be passed back to the System Client
     * @param mixed[] $debug JSONable debug data
     *
     * @return ResultData Result data set
     */
    public function okResult($message, $data = [], $debug = []): ResultData
    {
        return ResultData::create($data)
            ->setMessage($message)
            ->setDebug($debug);
    }

    /**
     * Throw an error to fail this provision function execution.
     *
     * @param string $message A user-friendly error message
     * @param mixed[] $data JSONable data to be passed back to the System Client
     * @param mixed[] $debugData JSONable debug data
     * @param Throwable $previous Previous exception, if any
     *
     * @throws ProvisionFunctionError
     *
     * @return no-return
     */
    public function errorResult($message, $data = [], $debug = [], $previous = null): void
    {
        throw (new ProvisionFunctionError($message, 0, $previous))
            ->withData($data)
            ->withDebug($debug);
    }

    /**
     * Set the Logger instance to facilitate debug logging.
     */
    final public function setLogger(LoggerInterface $logger): void
    {
        $this->log = $logger;
    }

    /**
     * Get the Logger instance for debug logging.
     */
    final public function getLogger(): LoggerInterface
    {
        if (!isset($this->log)) {
            throw new LogicException('Logger instance only set if Provider or Category implement LogsDebugData');
        }

        return $this->log;
    }

    /**
     * Sets the SystemInfo instance.
     */
    final public function setSystemInfo(SystemInfo $systemInfo): void
    {
        $this->systemInfo = $systemInfo;
    }

    /**
     * Get SystemInfo instance containing metadata about the system/runtime environment.
     */
    final public function getSystemInfo(): SystemInfo
    {
        if (!isset($this->systemInfo)) {
            throw new LogicException('SystemInfo only set if Provider or Category implement HasSystemInfo');
        }

        return $this->systemInfo;
    }
}
