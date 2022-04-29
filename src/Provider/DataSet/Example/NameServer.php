<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\DataSet\Example;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $host FQDN of the nameserver
 * @property-read string|null $ip IP of the nameserver
 */
class NameServer extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'host' => ['required', 'alpha-dash-dot'],
            'ip' => ['ip'],
        ]);
    }
}
