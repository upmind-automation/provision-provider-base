<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Registry\Data;

use Illuminate\Support\Arr;
use Upmind\ProvisionBase\Registry\Registry;

/**
 * Class encapsulating a buffered register method call.
 */
class BufferedRegister
{
    /**
     * Registry instance.
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Registry method name.
     *
     * @var string
     */
    protected $method;

    /**
     * Registry method arguments.
     *
     * @var mixed[]
     */
    protected $args;

    /**
     * @param string $method Method name
     * @param mixed[] $args Method arguments
     */
    public function __construct(Registry $registry, string $method, array $args)
    {
        $this->registry = $registry;
        $this->method = Arr::last(explode('::', $method));
        $this->args = $args;
    }

    /**
     * Return the register.
     */
    public function getRegistry(): Registry
    {
        return $this->registry;
    }

    /**
     * Return the buffered method name.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Return the buffered method arguments.
     *
     * @return mixed[]
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Execute this buffered register call on the given register instance.
     */
    public function execute(): void
    {
        call_user_func_array([$this->getRegistry(), $this->getMethod()], $this->getArgs());
    }

    public function __debugInfo()
    {
        return [
            'method' => $this->getMethod(),
            'args' => $this->getArgs(),
        ];
    }
}
