<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\DataSet;

class EmptyData extends DataSet
{
    final public static function rules(): Rules
    {
        return new Rules();
    }
}
