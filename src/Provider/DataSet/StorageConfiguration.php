<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\DataSet;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;

/**
 * @property-read string $base_path Storage base directory path
 * @property-read string $secret_key Base64-encoded secret key used for en/decryption
 */
class StorageConfiguration extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'base_path' => ['string', 'required'],
            'secret_key' => ['string', 'required'],
        ]);
    }
}
