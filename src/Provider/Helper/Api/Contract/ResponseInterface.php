<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Helper\Api\Contract;

use Psr\Http\Message\ResponseInterface as Psr7Response;

interface ResponseInterface
{
    /**
     * Construct the ApiResponse from a Psr7 Response.
     */
    public function __construct(Psr7Response $psr7Response, ?RequestInterface $request = null);

    /**
     * Returns the request responsible for this response, if available.
     */
    public function getRequest(): ?RequestInterface;

    /**
     * Whether the ApiResponse indicates success.
     */
    public function isSuccess(): bool;

    /**
     * Whether the Response was a redirect.
     */
    public function isRedirect(): bool;

    /**
     * Whether the response was a client error.
     */
    public function isClientError(): bool;

    /**
     * Whether the response was a server error.
     */
    public function isServerError(): bool;

    /**
     * Get the HTTP code of the response.
     */
    public function getHttpCode(): int;

    /**
     * Get a description/reason/explanation message about the response.
     */
    public function getMessage(): string;

    /**
     * Get the decoded response body.
     */
    public function getBody(): object;

    /**
     * Get the decoded response body as an array, and optionally return a specific
     * index or a specified default value.
     *
     * @return array|mixed
     */
    public function getBodyAssoc(?string $index = null, $default = null);
}
