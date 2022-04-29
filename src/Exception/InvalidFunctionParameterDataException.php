<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Exception;

/**
 * The given parameter data failed to pass the validation rules of the data set.
 */
class InvalidFunctionParameterDataException extends InvalidDataSetException
{
    public static function fromInvalidDataSet(InvalidDataSetException $e): self
    {
        return new self($e->validationException, 'The given provision function parameters were invalid');
    }
}
