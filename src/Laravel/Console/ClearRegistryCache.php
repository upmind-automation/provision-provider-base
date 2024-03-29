<?php

namespace Upmind\ProvisionBase\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Upmind\ProvisionBase\Laravel\ProvisionServiceProvider;

/**
 * Clear the provision registry cache.
 */
class ClearRegistryCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upmind:provision:clear {--without-summary : Don\'t output a registry summary}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the provision registry cache';

    /**
     * Execute the console command.
     */
    public function handle(CacheInterface $cache): int
    {
        $this->clearRegistry($cache);

        if (!$this->option('without-summary')) {
            $this->call('upmind:provision:summary');
        }

        return 0;
    }

    protected function clearRegistry(CacheInterface $cache)
    {
        if (!$cache->has(ProvisionServiceProvider::REGISTRY_CACHE_KEY)) {
            return;
        }

        $this->info('Clearing cached provision registry...');

        $cache->forget(ProvisionServiceProvider::REGISTRY_CACHE_KEY);

        $this->info('Done.');
    }
}
