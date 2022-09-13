<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel;

use Illuminate\Support\ServiceProvider as BaseProvider;
use Upmind\ProvisionBase\Laravel\Console;

/**
 * Register console commands.
 */
class ConsoleServiceProvider extends BaseProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\ListRegistry::class,
                Console\SummarizeRegistry::class,
                Console\CacheRegistry::class,
                Console\ClearRegistryCache::class,
            ]);
        }
    }
}
