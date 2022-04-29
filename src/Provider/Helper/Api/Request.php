<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Helper\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Upmind\ProvisionBase\Provider\Helper\Api\Contract\RequestInterface as ApiRequest;
use Upmind\ProvisionBase\Provider\Helper\Api\Contract\ResponseInterface as ApiResponse;

class Request implements ApiRequest
{
    /**
     * Fully namespaced classname of the Response object
     *
     * @var string
     */
    protected $responseClass;

    /**
     * Promise which resolves an Api\Response object
     *
     * @var \GuzzleHttp\Promise\PromiseInterface
     */
    protected $promise;

    /**
     * @var ApiResponse
     */
    protected $response;

    /**
     * @param ClientInterface $client Guzzle client
     * @param string $method HTTP verb
     * @param string $uri Endpoint uri
     * @param array $params Body parameters keyed by name
     */
    public function __construct(
        ClientInterface $client,
        string $method = 'GET',
        string $uri = '',
        array $params = [],
        array $requestOptions = []
    ) {
        $options = $requestOptions;

        if ($params) {
            $options['json'] = $params;
        }

        $this->promise = $client->requestAsync(
            strtoupper($method),
            $uri,
            $options
        )->then(function (Psr7Response $response) {
            $responseClass = $this->responseClass ?? Response::class;
            return $this->response = new $responseClass($response, $this);
        });
    }

    /**
     * @return PromiseInterface
     */
    public function getPromise(): PromiseInterface
    {
        return $this->promise;
    }

    /**
     * Returns the Api\Response object for this Request.
     *
     * @return ApiResponse
     */
    public function getResponse(): ApiResponse
    {
        if ($this->response) {
            return $this->response;
        }

        return $this->response = $this->getPromise()->wait();
    }
}
