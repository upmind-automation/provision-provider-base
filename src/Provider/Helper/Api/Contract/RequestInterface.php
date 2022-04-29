<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Helper\Api\Contract;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Upmind\ProvisionBase\Provider\Helper\Api\Contract\ResponseInterface as ApiResponse;

interface RequestInterface
{
    /**
     * Construct the Api Request object; send an async request wrapped in a Promise
     * which should resolve an Api Response.
     */
    public function __construct(
        ClientInterface $client,
        string $method = 'GET',
        string $uri = '',
        array $params = [],
        array $requestOptions = []
    );

    /**
     * Obtain the Api Response Promise.
     */
    public function getPromise(): PromiseInterface;

    /**
     * Resolve the Api Response from the Promise; blocking if necessary.
     */
    public function getResponse(): ApiResponse;
}
