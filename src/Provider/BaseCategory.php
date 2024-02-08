<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LogicException;
use Psr\Http\Message\MessageInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use Throwable;
use Upmind\ProvisionBase\Exception\ProvisionFunctionError;
use Upmind\ProvisionBase\Provider\Contract\LogsDebugData;
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
     * Array containing the history of guzzle requests for this instance.
     *
     * @var array<Message[]>
     */
    protected $guzzleHistory = [];

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

    /**
     * Get a guzzle handler stack which logs requests/responses if provider is
     * an instance of LogsDebugData and $debugLog === true. Requests and responses
     * will also be stored in $this->guzzleHistory.
     */
    protected function getGuzzleHandlerStack(bool $debugLog = false): HandlerStack
    {
        $stack = HandlerStack::create();

        $stack->push(Middleware::history($this->guzzleHistory));

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

    /**
     * Returns an assoc array of debug data for the last guzzle request/response
     * for guzzle clients whose stack was obtained from `$this->getGuzzleHandlerStack()`.
     *
     * @return array<array<string[]>>|null
     */
    protected function getLastGuzzleRequestDebug(): ?array
    {
        /** @var Request|null $lastRequest */
        $lastRequest = Arr::last($this->guzzleHistory)['request'] ?? null;
        /** @var Response|null $lastResponse */
        $lastResponse = Arr::last($this->guzzleHistory)['response'] ?? null;

        if (!$lastRequest) {
            return null;
        }

        $debug = [
            'last_request' => [
                'method' => $lastRequest->getMethod(),
                'url' => $lastRequest->getUri()->__toString(),
            ],
            'last_response' => null
        ];

        if ($lastResponse) {
            $debug['last_response'] = [
                'http_code' => $lastResponse->getStatusCode(),
                'body' => Str::limit($lastResponse->getBody()->__toString(), 300),
            ];
        }

        return $debug;
    }
}
