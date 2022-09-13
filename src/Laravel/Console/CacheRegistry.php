<?php

namespace Upmind\ProvisionBase\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Upmind\ProvisionBase\Laravel\ProvisionServiceProvider;
use Upmind\ProvisionBase\Registry\Registry;

/**
 * Clear, re-read + cache the provision registry.
 */
class CacheRegistry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upmind:provision:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the provision registry';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Registry $registry, CacheInterface $cache)
    {
        $this->cacheRegistry($registry, $cache);

        $this->call('upmind:provision:summary');

        return 0;
    }

    /**
     * @return bool Whether or not new registry data was written to cache
     */
    protected function cacheRegistry(Registry $registry, CacheInterface $cache): bool
    {
        if ($registry->wasCached()) {
            $oldHash = sha1($cache->get(ProvisionServiceProvider::REGISTRY_CACHE_KEY));
            $this->call('upmind:provision:clear');
        }

        if ($registry->getCategories()->isEmpty()) {
            return false;
        }

        $this->info('Storing provision registry in cache...');

        $serializedRegistry = serialize($registry);
        $newHash = sha1($serializedRegistry);

        $cache->forever(ProvisionServiceProvider::REGISTRY_CACHE_KEY, $serializedRegistry);

        $this->info('Done.');

        return !isset($oldHash) || $oldHash !== $newHash;
    }
}
