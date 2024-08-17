<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Upmind\ProvisionBase\Events\RegistryEventInterface;

class RegistryUpdatedEvent implements RegistryEventInterface
{
    use Dispatchable;

    public function getEventName(): string
    {
        return 'ProvisionRegistryUpdated';
    }
}
