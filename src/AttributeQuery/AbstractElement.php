<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\AttributeQuery;

use InvalidArgumentException;

abstract class AbstractElement implements ElementInterface
{
    /**
     * @var Query|null
     */
    protected $parentNode = null;

    /**
     * {@inheritDoc}
     */
    final public function getParent(): ?Query
    {
        return $this->parentNode;
    }

    /**
     * {@inheritDoc}
     */
    final public function setParent(Query $node): void
    {
        if ($node === $this) {
            throw new InvalidArgumentException('Query element node cannot be its own parent');
        }

        $this->parentNode = $node;
    }

    /**
     * {@inheritDoc}
     */
    final public static function isNode(): bool
    {
        return static::getType() === self::TYPE_NODE;
    }

    /**
     * {@inheritDoc}
     */
    final public static function isCondition(): bool
    {
        return static::getType() === self::TYPE_CONDITION;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
