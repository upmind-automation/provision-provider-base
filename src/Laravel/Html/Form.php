<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel\Html;

/**
 * Representation of a HTML form containing one or more FormElement.
 */
class Form
{
    /**
     * @var FormElement[]
     */
    protected $elements;

    /**
     * @param FormElement[] $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function elements(): array
    {
        return $this->elements;
    }

    public function __debugInfo()
    {
        return [
            'elements' => $this->elements,
        ];
    }
}
