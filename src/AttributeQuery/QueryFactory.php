<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\AttributeQuery;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Upmind\ProvisionBase\Laravel\AttributeQueryValidator;

/**
 * @deprecated
 */
class QueryFactory
{
    /**
     * @param ElementInterface[]|callable $all Child elements or callable for appending child elements
     */
    public static function all($all = []): Query
    {
        $node = new Query(Query::NODE_TYPE_ALL);

        if (is_callable($all)) {
            $all($node);
        } else {
            foreach (Arr::wrap($all) as $child) {
                $node->addChild($child);
            }
        }

        return $node;
    }

    /**
     * @param ElementInterface[]|callable $any Child elements or callable for appending child elements
     */
    public static function any($any = []): Query
    {
        $node = new Query(Query::NODE_TYPE_ANY);

        if (is_callable($any)) {
            $any($node);
        } else {
            foreach (Arr::wrap($any) as $child) {
                $node->addChild($child);
            }
        }

        return $node;
    }

    public static function fromArray(array $query): Query
    {
        $nodeType = $query['node'];
        $conditions = array_map(function ($element) {
            if ($element['type'] === Query::getType()) {
                return self::fromArray($element);
            } elseif ($element['type'] === Condition::getType()) {
                return new Condition($element['attribute'], $element['value'] ?? null);
            }
        }, $query['conditions'] ?? []);

        return new Query($nodeType, $conditions);
    }

    /**
     * @throws ValidationException
     */
    public static function validateArray($query, string $validationKey = 'provision_attribute_query'): void
    {
        ValidatorFactory::make(
            [$validationKey => $query],
            [$validationKey => AttributeQueryValidator::RULE_NAME]
        )->validate();
    }
}
