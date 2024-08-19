<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel\Events;

use Upmind\ProvisionBase\Events\RegistryEventInterface;

class RegistryUpdatedEvent implements RegistryEventInterface
{
    public function getEventName(): string
    {
        return 'ProvisionRegistryUpdated';
    }
}
