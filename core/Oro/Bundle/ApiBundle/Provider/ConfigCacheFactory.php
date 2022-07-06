<?php

namespace Oro\Bundle\ApiBundle\Provider;

use Oro\Component\Config\Cache\ConfigCacheStateInterface;

/**
 * The factory to create an object is used to store API configuration cache.
 */
class ConfigCacheFactory
{
    /** @var string */
    private $cacheDir;

    /** @var bool */
    private $debug;

    /** @var ConfigCacheWarmer */
    private $configCacheWarmer;

    /** @var ConfigCacheStateInterface[]|null */
    private $dependencies;

    /**
     * @param string $cacheDir
     * @param bool   $debug
     */
    public function __construct(string $cacheDir, bool $debug)
    {
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * @param ConfigCacheWarmer $configCacheWarmer
     */
    public function setConfigCacheWarmer(ConfigCacheWarmer $configCacheWarmer): void
    {
        $this->configCacheWarmer = $configCacheWarmer;
    }

    /**
     * @param string $configKey
     *
     * @return ConfigCacheFile
     */
    public function getCache(string $configKey): ConfigCacheFile
    {
        $cache = new ConfigCacheFile(
            sprintf('%s/%s.php', $this->cacheDir, $configKey),
            $this->debug,
            $configKey,
            $this->configCacheWarmer
        );
        if ($this->dependencies) {
            foreach ($this->dependencies as $dependency) {
                $cache->addDependency($dependency);
            }
        }

        return $cache;
    }

    /**
     * Registers a cache the API configuration cache depends on.
     *
     * @param ConfigCacheStateInterface $configCache
     */
    public function addDependency(ConfigCacheStateInterface $configCache): void
    {
        $this->dependencies[] = $configCache;
    }
}
