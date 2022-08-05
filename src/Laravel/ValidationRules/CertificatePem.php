<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel\ValidationRules;

use Illuminate\Validation\Validator;

/**
 * Attribute must be a certificate PEM.
 */
class CertificatePem
{
    /**
     * Certificate PEM regex pattern.
     *
     * @var string
     *
     * Inspired by @link https://regex101.com/r/mGnr7I/1
     */
    public const PATTERN = '/^(-----BEGIN (PUBLIC KEY|(RSA )?PRIVATE KEY|CERTIFICATE)-----(\n|\r|\r\n)' // header
        . '([0-9a-zA-Z\+\/=]{64}(\n|\r|\r\n))*([0-9a-zA-Z\+\/=]{1,63}(\n|\r|\r\n))?' // content
        . '-----END (PUBLIC KEY|(RSA )?PRIVATE KEY|CERTIFICATE)-----(\n|\r|\r\n)?)+$/'; // footer

    /**
     * @param string $attribute Attribute name
     * @param integer|float|string|null $value Attribute value
     * @param string[] $parameters Array of validation rule parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        if (!is_string($value)) {
            $validator->addFailure($attribute, 'string');
            return true; //return true since we manually added a message to the error bag
        }

        return preg_match(self::PATTERN, $value);
    }
}
