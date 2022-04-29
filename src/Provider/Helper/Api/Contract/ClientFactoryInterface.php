<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Helper\Api\Contract;

use GuzzleHttp\ClientInterface;

interface ClientFactoryInterface
{
    /**
     * Makes and returns a HTTP client for the given Provider Configuration array.
     *
     * @param array $configuration Provider Configuration array
     *
     * @return ClientInterface An HTTP client
     */
    public static function make(array $configuration): ClientInterface;
}
