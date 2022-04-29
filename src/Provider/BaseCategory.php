<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use LogicException;
use Psr\Http\Message\MessageInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;
use Upmind\ProvisionBase\Exception\ProvisionFunctionError;
use Upmind\ProvisionBase\Provider\Contract\LogsDebugData;
use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\Storage\Storage;

/**
 * The root class all Categories should extend from
 */
abstract class BaseCategory implements Contract\CategoryInterface
{
    /**
     * Filesystem storage access.
     *
     * @var Storage|null
     */
    protected $store;

    /**
     * Debug logger.
     *
     * @var LoggerInterface|null
     */
    protected $log;

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
     * Set the Store instance to permit filesystem access.
     */
    final public function setStorage(Storage $store): void
    {
        $this->store = $store;
    }

    /**
     * Get the Storage instance for filesystem access. A Store instance will only
     * be set if the Category or Provider implement the StoresFiles interface.
     */
    final public function getStorage(): Storage
    {
        if (!isset($this->store)) {
            throw new LogicException('Storage instance only set if Provider or Category implement StoresFiles');
        }

        return $this->store;
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
     * Get a guzzle handler stack which logs requests/responses if provider is
     * an instance of LogsDebugData and $debugLog === true.
     */
    protected function getGuzzleHandlerStack(bool $debugLog = false): HandlerStack
    {
        $stack = HandlerStack::create();

        if (!$debugLog || !$this instanceof LogsDebugData) {
            return $stack;
        }

        // Rewinds HTTP message body seek position after the stream has been read by Logger middleware
        $rewindMessageBody = function (MessageInterface $message) {
            $message->getBody()->rewind();
            return $message;
        };
        // Logs Request/Response HTTP messages
        $logger = Middleware::log(
            $this->getLogger(),
            new MessageFormatter(MessageFormatter::DEBUG . PHP_EOL),
            LogLevel::DEBUG
        );

        $stack->push(Middleware::mapRequest($rewindMessageBody), 'Rewind-Request-Stream-After-Logging');
        $stack->push(Middleware::mapResponse($rewindMessageBody), 'Rewind-Response-Stream-After-Logging');
        $stack->push($logger, 'Logger');

        return $stack;
    }
}
