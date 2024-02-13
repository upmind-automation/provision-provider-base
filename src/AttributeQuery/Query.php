<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\AttributeQuery;

use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * Attribute query node.
 *
 * @deprecated
 */
class Query extends AbstractElement
{
    /**
     * @var string
     */
    public const NODE_TYPE_ANY = 'ANY';

    /**
     * @var string
     */
    public const NODE_TYPE_ALL = 'ALL';

    /**
     * @var string
     */
    protected $nodeType;

    /**
     * @var ElementInterface[]
     */
    protected $childElements = [];

    /**
     * @param string $nodeType One of self::NODE_TYPE_ANY or self::NODE_TYPE_ALL
     * @param ElementInterface[]|Query $childElements
     */
    public function __construct(string $nodeType = self::NODE_TYPE_ALL, $childElements = [])
    {
        if (!in_array($nodeType, [self::NODE_TYPE_ALL, self::NODE_TYPE_ANY])) {
            throw new InvalidArgumentException(sprintf('Invalid node type %s', $nodeType));
        }

        $this->nodeType = $nodeType;

        foreach (Arr::wrap($childElements) as $element) {
            $this->addChild($element);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return static::TYPE_NODE;
    }

    /**
     * Get this element's node type; one of self::NODE_TYPE_ANY or self::NODE_TYPE_ALL
     */
    public function getNodeType(): string
    {
        return $this->nodeType;
    }

    /**
     * Get this node's child Query and Condition elements.
     *
     * @return ElementInterface[]|Query[]|Condition[]
     */
    public function getConditions(): array
    {
        return $this->childElements;
    }

    /**
     * Returns child Condition elements including those of child Query elements.
     *
     * @return Condition[]
     */
    public function getAllQueryConditions(): array
    {
        $conditions = [];

        foreach ($this->getConditions() as $child) {
            if ($child instanceof Query) {
                $conditions = array_merge($conditions, $child->getAllQueryConditions());
                continue;
            }

            $conditions[] = $child;
        }

        return $conditions;
    }

    /**
     * Add a child query element to this node.
     *
     * @return self $this
     */
    public function addChild(ElementInterface $element): self
    {
        if ($element === $this) {
            throw new InvalidArgumentException('Query element node cannot be its own child');
        }

        $element->setParent($this);
        $this->childElements[] = $element;

        return $this;
    }

    /**
     * @param ElementInterface[]|callable<Query> $all Child elements or callable for appending child elements
     *
     * @return self $this
     */
    public function all($all = []): self
    {
        $node = new self(self::NODE_TYPE_ALL);

        if (is_callable($all)) {
            $all($node);
        } else {
            foreach (Arr::wrap($all) as $child) {
                $node->addChild($child);
            }
        }

        $this->addChild($node);

        return $this;
    }

    /**
     * @param ElementInterface[]|callable<Query> $any Child elements or callable for appending child elements
     *
     * @return self $this
     */
    public function any($any = []): self
    {
        $node = new Query(self::NODE_TYPE_ANY);

        if (is_callable($any)) {
            $any($node);
        } else {
            foreach (Arr::wrap($any) as $child) {
                $node->addChild($child);
            }
        }

        $this->addChild($node);

        return $this;
    }

    /**
     * Add a child condition
     *
     * @return self $this
     */
    public function condition(string $attribute, $value = null): self
    {
        $this->addChild(new Condition($attribute, $value));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'node' => $this->getNodeType(),
            'conditions' => array_map(function (ElementInterface $element) {
                return $element->toArray();
            }, $this->getConditions()),
        ];
    }

    public function __debugInfo()
    {
        return [
            'type' => $this->getType(),
            'node' => $this->getNodeType(),
            'conditions' => $this->getConditions(),
        ];
    }
}
