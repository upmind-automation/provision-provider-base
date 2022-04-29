<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Helper\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Arr;
use Upmind\ProvisionBase\Provider\Helper\Api\Contract\ClientFactoryInterface;
use Upmind\ProvisionBase\Provider\Helper\Exception\ConfigurationError;

class ClientFactory implements ClientFactoryInterface
{
    /**
     * Instantiate a Guzzle Client from the Provider's Configuration array.
     *
     * @param array $configuration Provider Configuration array
     *
     * @return ClientInterface
     */
    public static function make(array $configuration): ClientInterface
    {
        static::checkConfiguration($configuration);

        $options = static::getGuzzleOptions($configuration);

        return new Client($options);
    }

    /**
     * @param array $configuration Provider Configuration array
     * @param array $requestOptions Guzzle Request options
     *
     * @return array Guzzle Request options
     */
    protected static function getGuzzleOptions(array $configuration, array $requestOptions = []): array
    {
        $hostname = Arr::get($configuration, 'hostname');

        return array_merge([
            'base_uri' => "https://{$hostname}",
            'headers' => [
                'Accept' => 'application/json'
            ],
            'timeout' => 10,
            'http_errors' => false,
            'allow_redirects' => false,
        ], $requestOptions);
    }

    /**
     * Ensure the given Provider Configuration contains the necessary fields to
     * construct a Guzzle Client.
     *
     * @throws ConfigurationError
     */
    protected static function checkConfiguration(array $configuration)
    {
        $requiredFields = [
            'hostname'
        ];

        $missingFields = collect($requiredFields)
            ->diff(array_keys($configuration));

        if ($missingFields->isNotEmpty()) {
            throw ConfigurationError::forMissingData($missingFields->all());
        }
    }
}
