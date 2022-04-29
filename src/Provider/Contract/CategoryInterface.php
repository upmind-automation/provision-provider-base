<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Contract;

use Upmind\ProvisionBase\Provider\DataSet\AboutData;

/**
 * Root interface which all Categories must implement.
 *
 * Categories must define their available provision functions as abstract methods
 * which type hint a sub-class of DataSet as their sole parameter, and return a
 * sub-class of ResultSet. These methods must therefore each be implemented by
 * all of the Providers of this Category.
 */
interface CategoryInterface
{
    /**
     * Returns information about the category such as name + description.
     */
    public static function aboutCategory(): AboutData;
}
