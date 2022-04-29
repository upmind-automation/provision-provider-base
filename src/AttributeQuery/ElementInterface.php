<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\AttributeQuery;

use JsonSerializable;

interface ElementInterface extends JsonSerializable
{
    /**
     * @var string
     */
    public const TYPE_NODE = 'node';

    /**
     * @var string
     */
    public const TYPE_CONDITION = 'condition';

    /**
     * Returns one of static::TYPE_NODE or static::TYPE_CONDITION
     */
    public static function getType(): string;

    /**
     * Determine whether this is a query node.
     */
    public static function isNode(): bool;

    /**
     * Determine whether this is a query condition.
     */
    public static function isCondition(): bool;

    /**
     * Get this element's parent node.
     */
    public function getParent(): ?Query;

    /**
     * Set this element's parent node.
     */
    public function setParent(Query $node): void;

    /**
     * Convert the element to an array.
     */
    public function toArray(): array;
}
