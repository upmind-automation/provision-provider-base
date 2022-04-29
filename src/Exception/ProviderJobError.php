<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Exception;

use Upmind\ProvisionBase\ProviderJob;
use Upmind\ProvisionBase\Exception\Contract\ProviderJobErrorInterface;

class ProviderJobError extends \RuntimeException implements ProviderJobErrorInterface
{
    /**
     * @var \Upmind\ProvisionBase\ProviderJob
     */
    protected $job;

    /**
     * Set the subject ProviderJob instance on the exception
     *
     * @param \Upmind\ProvisionBase\ProviderJob $job
     *
     * @return ProviderJobErrorInterface
     */
    public function withProviderJob(ProviderJob $job): ProviderJobErrorInterface
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get the subject ProviderJob instance from the exception
     *
     * @return \Upmind\ProvisionBase\ProviderJob
     */
    public function getProviderJob(): ProviderJob
    {
        return $this->job;
    }
}
