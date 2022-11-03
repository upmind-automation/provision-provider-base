<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel\Html;

/**
 * Encapsulation of a set of HTML form fields.
 */
class FormGroup extends FormElement
{
    /**
     * @var FormElement[]
     */
    protected $elements;

    /**
     * @param string $name
     * @param bool $required
     * @param FormElement[] $elements
     * @param array<string[]> $validationRules
     */
    public function __construct(
        string $name,
        bool $required = false,
        array $elements = [],
        array $validationRules = [],
        ?FormGroup $parentGroup = null
    ) {
        $this->name = $name;
        $this->required = $required;
        $this->elements = $elements;
        $this->validationRules = $validationRules;
        $this->group = $parentGroup;

        array_walk($this->elements, function (FormElement $element) {
            $element->setGroup($this);
        });
    }

    public function type(): string
    {
        return self::TYPE_GROUP;
    }

    /**
     * @return FormElement[]
     */
    public function elements(): array
    {
        return $this->elements;
    }

    public function __debugInfo()
    {
        return [
            'name' => $this->name(),
            'relative_name' => $this->relativeName(),
            'required' => $this->required(),
            'elements' => $this->elements(),
        ];
    }
}
