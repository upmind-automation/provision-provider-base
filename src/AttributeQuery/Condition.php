<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\AttributeQuery;

use InvalidArgumentException;

/**
 * @deprecated
 */
class Condition extends AbstractElement
{
    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param string $attribute
     * @param mixed $value
     */
    public function __construct(string $attribute, $value = null)
    {
        if (empty($attribute)) {
            throw new InvalidArgumentException('Condition attribute name cannot be empty');
        }

        $this->attribute = $attribute;
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return self::TYPE_CONDITION;
    }

    /**
     * Get this condition's attribute name.
     */
    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * Get this condition's value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set this condition's value.
     *
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'attribute' => $this->getAttribute(),
            'value' => $this->getValue(),
        ];
    }

    public function __debugInfo()
    {
        return $this->toArray();
    }
}
