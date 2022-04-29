<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Exception;

/**
 * The given data failed to pass the validation rules of the provider configuration's
 * data set.
 */
class InvalidProviderConfigurationException extends InvalidDataSetException
{
    public static function fromInvalidDataSet(InvalidDataSetException $e): self
    {
        return new self($e->validationException, 'Provider configuration error');
    }
}
