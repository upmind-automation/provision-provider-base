<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Result\Contract;

use JsonSerializable;

/**
 * Interface implemented by provision Result objects
 */
interface ResultInterface extends JsonSerializable
{
    /**
     * @var string
     */
    public const STATUS_OK = 'ok';

    /**
     * @var string
     */
    public const STATUS_ERROR = 'error';

    public static function createFromJson(string $json): self;

    public static function createFromArray(array $array): self;

    public function __construct(string $status, ?string $message = null, ?array $data = null, ?array $debug = null);

    public function getStatus(): string;

    public function getMessage(): string;

    public function getData(): ?array;

    public function getDebug(): ?array;
}
