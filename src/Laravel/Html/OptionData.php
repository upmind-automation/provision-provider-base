<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel\Html;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * HTML field select/radio option data set.
 *
 * @property-read string $label Human-readable option label
 * @property-read mixed $value Option value
 */
class OptionData extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'label' => ['required', 'string'],
            'value' => ['present'],
        ]);
    }
}
