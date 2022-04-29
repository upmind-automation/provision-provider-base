<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Contract;

use Upmind\ProvisionBase\Provider\Storage\Storage;

interface StoresFiles
{
    /**
     * Set the Store instance to permit filesystem access.
     */
    public function setStorage(Storage $store): void;

    /**
     * Get the Store instance for filesystem access. A Store instance will only
     * be set if the Category or Provider implement the StoresFiles interface.
     */
    public function getStorage(): Storage;
}
