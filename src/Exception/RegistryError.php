<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Exception;

use Upmind\ProvisionBase\Exception\Contract\ProvisionException;

class RegistryError extends \LogicException implements ProvisionException
{
    public static function forInvalidCategoryClass($class): self
    {
        return new static("Provision Category {$class} does not implement CategoryInterface");
    }

    public static function forInvalidProviderClass($class): self
    {
        return new static("Provision Provider {$class} does not implement ProviderInterface");
    }

    public static function forInexistentCategoryClass($class): self
    {
        return new static("Provision Category class {$class} does not exist");
    }

    public static function forInexistentProviderClass($class): self
    {
        return new static("Provision Provider class {$class} does not exist");
    }

    public static function forProviderClassNotSubclassOfCategory($category, $provider): self
    {
        return new static("Provision Provider {$provider} is not a subclass of Category {$category}");
    }

    public static function forUninstantiableProviderClass($class): self
    {
        return new static("Provision Provider {$class} is not instantiable");
    }

    public static function forUndefinedProviderFunctions($category, $provider, array $undefinedFunctions): self
    {
        return new static(
            "Provision Provider {$provider} does not provide the following "
                . "functions required by Category {$category}: "
                . implode(', ', $undefinedFunctions)
        );
    }

    public static function forDuplicateCategoryAlias($alias, $existingClass): self
    {
        return new static("Provision Category alias {$alias} is already being used by {$existingClass}");
    }

    public static function forDuplicateProviderAlias($alias, $existingClass): self
    {
        return new static("Provision Provider alias {$alias} is already being used by {$existingClass}");
    }

    public static function forAlreadyInstantiated(): self
    {
        return new static("Provision Registry instance has already been set");
    }
}
