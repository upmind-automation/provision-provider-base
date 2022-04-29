<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel;

use Upmind\ProvisionBase\AttributeQuery\Query;
use Upmind\ProvisionBase\AttributeQuery\QueryFactory;

class AttributeQueryCast
{
    /**
     * @param string|null $queryJson AttributeQuery json string
     */
    public static function fromJson(?string $queryJson): ?Query
    {
        if (empty($queryJson)) {
            return null;
        }

        $queryArray = json_decode($queryJson, true);

        if (empty($queryArray) || !is_array($queryArray)) {
            return null;
        }

        return QueryFactory::fromArray($queryArray);
    }

    /**
     * @param string|array|Query|null $query Json string, array or AttributeQuery object
     *
     * @return string|null AttributeQuery json string
     */
    public static function toJson($query, string $attributeName = 'provider_configuration_query'): ?string
    {
        if (is_string($query) && !empty($query)) {
            $query = json_decode($query, true);
        }

        if (empty($query)) {
            return null;
        }

        if (!$query instanceof Query) {
            QueryFactory::validateArray($query, $attributeName);
        }

        return json_encode($query);
    }
}
