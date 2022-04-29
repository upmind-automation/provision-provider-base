<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\DataSet\Example;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $username Username of the shared hosting account
 * @property-read string $password Desired new password
 */
class ResetPasswordParameterSet extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'username' => ['required', 'alpha_num'],
            'password' => ['required', 'string'],
        ]);
    }
}
