<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Exception;

use Upmind\ProvisionBase\ProviderJob;

class InvalidProviderJob extends ProviderJobError
{
    /**
     * Thrown if the Provider has not defined the requested function.
     *
     * @param \Upmind\ProvisionBase\ProviderJob $job
     *
     * @return self
     */
    public static function forInvalidFunction(ProviderJob $job): self
    {
        $function = $job->getFunction();
        $message = "Requested function {$function} is not defined";
        return (new self($message))->withProviderJob($job);
    }
}
