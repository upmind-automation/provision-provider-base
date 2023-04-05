<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\DataSet;

/**
 * Metadata about the system / runtime environment.
 *
 * @property-read string[] $outgoing_ips List of IP addresses used for outgoing connections
 */
class SystemInfo extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'outgoing_ips' => ['required', 'array'],
            'outgoing_ips.*' => ['required', 'ip'],
        ]);
    }
}
