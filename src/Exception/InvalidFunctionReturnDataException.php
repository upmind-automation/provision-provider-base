<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Exception;

/**
 * The given return data failed to pass the validation rules of the data set.
 */
class InvalidFunctionReturnDataException extends InvalidDataSetException
{
    public static function fromInvalidDataSet(InvalidDataSetException $e): self
    {
        return new self($e->validationException, 'Unexpected provision provider return data');
    }
}
