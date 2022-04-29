<?php

namespace Upmind\ProvisionBase\Laravel;

use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Upmind\ProvisionBase\AttributeQuery\Condition;
use Upmind\ProvisionBase\AttributeQuery\Query;

/**
 * Attribute query must conform to a specific structure.
 */
class AttributeQueryValidator
{
    /**
     * @var string
     */
    public const RULE_NAME = 'provision_attribute_query';

    /**
     * @param string $attribute Attribute name
     * @param integer|float|string|null $value Attribute value
     * @param string[] $parameters Array of validation rule parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateAttributeQuery($attribute, $value, $parameters, $validator)
    {
        if (!is_array($value)) {
            return false;
        }

        $errors = collect(ValidatorFactory::make($value, $this->getRules())->errors()->toArray())
            ->mapWithKeys(function ($errors, $key) use ($attribute) {
                //prefix errors key by attribute name
                return [sprintf('%s.%s', $attribute, $key) => $errors];
            })
            ->all();

        $validator->errors()->merge($errors);

        return true;
    }

    /**
     * Get the recursive validation rules for an Attribute Query.
     *
     * @return array<string[]>
     */
    public function getRules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                'in:' . implode(',', [
                    Query::getType(),
                    Condition::getType(),
                ]),
            ],
            'node' => [
                'required_if:type,' . Query::getType(),
                'string',
                'in:' . implode(',', [
                    Query::NODE_TYPE_ALL,
                    Query::NODE_TYPE_ANY,
                ]),
            ],
            'conditions' => [
                'required_if:type,' . Query::getType(),
                'array',
            ],
            'conditions.*' => [
                self::RULE_NAME,
            ],
            'attribute' => [
                'required_if:type,' . Condition::getType(),
                'string',
            ],
            'value' => [
                // 'required_if:type,' . Condition::getType(),
                'nullable',
            ],
        ];
    }
}
