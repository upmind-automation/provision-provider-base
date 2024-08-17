<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Events;

interface RegistryEventInterface
{
    public function getEventName(): string;
}
