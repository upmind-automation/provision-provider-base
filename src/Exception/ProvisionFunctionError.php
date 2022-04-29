<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Exception;

use Illuminate\Contracts\Support\Arrayable;
use Throwable;
use Upmind\ProvisionBase\Result\ProviderResult;

/**
 * Exception representing a runtime error encountered during provision function execution.
 */
class ProvisionFunctionError extends ProviderJobError
{
    /**
     * Error data.
     *
     * @var array
     */
    protected $data;

    /**
     * Error debug data.
     *
     * @var array
     */
    protected $debug;

    public static function create(string $message, ?Throwable $previous = null): self
    {
        return new self($message, $previous ? $previous->getCode() : 0, $previous);
    }

    public function withData($data = []): self
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $this->data = is_array($data) ? $data : [];
        return $this;
    }

    public function getData(): array
    {
        return $this->data ?? [];
    }

    public function withDebug($debug = []): self
    {
        if ($debug instanceof Arrayable) {
            $debug = $debug->toArray();
        }

        $this->debug = is_array($debug) ? $debug : [];
        return $this;
    }

    public function getDebug(): array
    {
        return $this->debug ?? [];
    }

    /**
     * Create a successful provision result from this result data instance.
     */
    final public function toProvisionResult(): ProviderResult
    {
        return new ProviderResult(
            ProviderResult::STATUS_ERROR,
            $this->getMessage(),
            $this->getData(),
            $this->getDebug()
        );
    }
}
