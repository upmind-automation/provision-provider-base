<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Exception\Contract;

use Upmind\ProvisionBase\ProviderJob;
use Upmind\ProvisionBase\Exception\Contract\ProvisionException;

/**
 * Marker interface for ProviderJob Exceptions
 */
interface ProviderJobErrorInterface extends ProvisionException
{
    public function withProviderJob(ProviderJob $job): self;
    public function getProviderJob(): ProviderJob;
}
