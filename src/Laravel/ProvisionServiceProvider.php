<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel;

use Illuminate\Support\ServiceProvider as BaseProvider;
use Throwable;
use Upmind\ProvisionBase\Registry\Registry;
use Upmind\ProvisionBase\Exception\RegistryError;

/**
 * A laravel service provider with methods for binding Upmind Provisioning
 * Categories and Providers to the Provision Registry.
 */
class ProvisionServiceProvider extends BaseProvider
{
    /**
     * @var string
     */
    public const REGISTRY_CACHE_KEY = 'Upmind/Provision/Registry';

    /**
     * Binds a Category to the provision registry.
     *
     * @param string $identifier Unique category identifier
     * @param string $class Fully namespaced category class name
     *
     * @return void
     */
    final protected function bindCategory(string $identifier, string $class): void
    {
        try {
            $this->provisionRegistry()->registerCategory($identifier, $class);
        } catch (Throwable $e) {
            throw new RegistryError(
                "Provision Category Bind Error. " . get_class($e) . ": " . $e->getMessage(),
                intval($e->getCode()),
                $e
            );
        }
    }

    /**
     * Binds a Provider to the provision registry.
     *
     * @param string $category Category identifier or class name
     * @param string $identifier Unique provider identifier
     * @param string $class Fully namespaced provider class name
     *
     * @return void
     */
    final protected function bindProvider(string $category, string $identifier, string $class): void
    {
        try {
            $this->provisionRegistry()->registerProvider($category, $identifier, $class);
        } catch (Throwable $e) {
            throw new RegistryError(
                "Provision Provider Bind Error. " . get_class($e) . ": " . $e->getMessage(),
                intval($e->getCode()),
                $e
            );
        }
    }

    /**
     * Obtain singleton instance of the provision registry.
     */
    protected function provisionRegistry(): Registry
    {
        return $this->app->make(Registry::class);
    }

    public function register()
    {
        // Bind registry as singleton to container
        $this->app->singleton(Registry::class, function () {
            return Registry::getInstance();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Registry::class];
    }
}
