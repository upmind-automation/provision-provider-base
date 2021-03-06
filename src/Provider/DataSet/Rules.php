<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\DataSet;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Provision data set validation rules
 */
final class Rules implements ArrayAccess, JsonSerializable, Arrayable, Jsonable
{
    /**
     * Raw data set rules.
     *
     * @var array<string[]>
     */
    protected $rawRules;

    /**
     * Expanded data set rules.
     *
     * @var array<string[]>|null
     */
    protected $expandedRules;

    /**
     * Name of the parent field, if any.
     *
     * @var string|null
     */
    protected $parentField;

    /**
     * @param array<string[]> $rules Laravel validation rules and nested data set references
     */
    public function __construct(array $rules = [])
    {
        $this->rawRules = $rules;
        $this->expandedRules = null;
    }

    /**
     * Create a new rules instance.
     */
    public static function fromArray(array $rules): self
    {
        return new self($rules);
    }

    /**
     * Set the parent field for rule expansion
     */
    public function setParentField(?string $parentField): self
    {
        if ($this->parentField !== $parentField) {
            $this->parentField = $parentField ?: null;
            $this->expandedRules = null;
        }

        return $this;
    }

    /**
     * Expand these data set rules' nested references and return a portable rule
     * set.
     *
     * @param string|null $parentField Parent field name for rule expansion
     *
     * @return array<string[]> Expanded rules
     */
    public function expand(?string $parentField = null): array
    {
        $this->setParentField($parentField);

        if (!isset($this->expandedRules)) {
            $this->expandedRules = RuleParser::expand($this->rawRules, $this->parentField);
        }

        return $this->expandedRules;
    }

    /**
     * Return the raw data set rules, optionally for the given field.
     *
     *@param string|null $field Optionally return rules for this field only
     *
     * @return array<string[]>|string[] Raw rules
     */
    public function raw(?string $field = null): array
    {
        if (isset($field)) {
            return RuleParser::explodeRules($this->rawRules[$field] ?? []);
        }

        return $this->rawRules;
    }

    /**
     * Get the set parent field name, if any.
     *
     * @return string|null
     */
    public function parentField(): ?string
    {
        return $this->parentField;
    }

    /**
     * @return bool
     */
    public function offsetExists($offset)
    {
        $this->expand($this->parentField);

        return array_key_exists($offset, $this->expandedRules);
    }

    /**
     * @return string[]
     */
    public function offsetGet($offset)
    {
        $this->expand($this->parentField);

        return $this->expandedRules[$offset];
    }

    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->expand($this->parentField);

        $this->expandedRules[$offset] = $value;
    }

    /**
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->expand($this->parentField);

        unset($this->expandedRules[$offset]);
    }

    /**
     * Return the expanded rules in array format.
     *
     * @return array
     */
    public function toArray()
    {
        $this->expand($this->parentField);

        return $this->expandedRules;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Return the expanded rules in json format.
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options | JSON_THROW_ON_ERROR);
    }

    public function __debugInfo()
    {
        return $this->expandedRules ?? $this->rawRules;
    }
}
