<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Helper\Exception;

use Upmind\ProvisionBase\Provider\Helper\Exception\Contract\ProviderError;

class ConfigurationError extends \RuntimeException implements ProviderError
{
    public static function forMissingData(array $fieldNames): ProviderError
    {
        return new static("Configuration data missing: " . implode(', ', $fieldNames));
    }
}
