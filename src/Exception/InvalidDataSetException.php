<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Exception;

use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Upmind\ProvisionBase\Exception\Contract\ProvisionException;

/**
 * The given data failed to pass the validation rules for this type of dataset
 */
class InvalidDataSetException extends InvalidArgumentException implements ProvisionException
{
    /**
     * Optional error key prefix.
     *
     * @var string|null
     */
    protected $errorPrefix;

    /**
     * @param ValidationException $validationException
     */
    protected $validationException;

    public function __construct(ValidationException $validationException, ?string $message = null)
    {
        $this->validationException = $validationException;

        parent::__construct($message ?: $validationException->getMessage(), 0, $validationException);
    }

    /**
     * @return static
     */
    public function setErrorPrefix(?string $prefix)
    {
        $this->errorPrefix = $prefix ?: null;

        return $this;
    }

    /**
     * @return string[]
     */
    public function errors(): array
    {
        $prefix = isset($this->errorPrefix)
            ? rtrim($this->errorPrefix, '.')
            : null;

        if (empty($prefix)) {
            return $this->validationException->errors();
        }

        return collect($this->validationException->errors())
            ->mapWithKeys(function ($error, $key) use ($prefix) {
                return [
                    sprintf('%s.%s', $prefix, ltrim($key, '.')) => $error
                ];
            })
            ->all();
    }
}
