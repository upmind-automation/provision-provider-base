<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Registry\Data;

/**
 * Interface for registered entities.
 */
interface RegisterInterface
{
    /**
     * Determine whether the given identifier or instance matches this register.
     *
     * @param RegisterInterface|string $register
     */
    public function is($register): bool;

    /**
     * Enumerate all data for this register.
     */
    public function enumerateData(): void;
}
