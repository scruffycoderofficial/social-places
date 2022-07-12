<?php

namespace Oro\Bundle\ApiBundle\Provider;

use Oro\Component\Config\Cache\ConfigCacheStateInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * A cache for API configuration.
 */
class ConfigCache implements ConfigCacheStateInterface
{
    /** @var string */
    private $configKey;

    /** @var bool */
    private $debug;

    /** @var ConfigCacheFactory */
    private $configCacheFactory;

    /** @var array|null */
    private $data;

    /** @var ConfigCacheFile|null */
    private $cache;

    /**
     * @var int|bool|null
     * * FALSE if the timestamp is not retrieved yet
     * * NULL if cache file does not exist
     * * an integer for the timestamp of existing cache file
     */
    private $cacheTimestamp = false;

    /** @var bool|null */
    private $cacheFresh;

    /**
     * @param string             $configKey
     * @param bool               $debug
     * @param ConfigCacheFactory $configCacheFactory
     */
    public function __construct(
        string $configKey,
        bool $debug,
        ConfigCacheFactory $configCacheFactory
    ) {
        $this->configKey = $configKey;
        $this->debug = $debug;
        $this->configCacheFactory = $configCacheFactory;
    }

    /**
     * @param string $configFile
     *
     * @return array
     */
    public function getConfig(string $configFile): array
    {
        $configs = $this->getSection(ConfigCacheWarmer::CONFIG);
        if (!isset($configs[$configFile])) {
            throw new \InvalidArgumentException(sprintf('Unknown config "%s".', $configFile));
        }

        return $configs[$configFile];
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return $this->getSection(ConfigCacheWarmer::ALIASES);
    }

    /**
     * @return string[]
     */
    public function getExcludedEntities(): array
    {
        return $this->getSection(ConfigCacheWarmer::EXCLUDED_ENTITIES);
    }

    /**
     * @return array [class name => substitute class name, ...]
     */
    public function getSubstitutions(): array
    {
        return $this->getSection(ConfigCacheWarmer::SUBSTITUTIONS);
    }

    /**
     * @return array
     */
    public function getExclusions(): array
    {
        return $this->getSection(ConfigCacheWarmer::EXCLUSIONS);
    }

    /**
     * @return array
     */
    public function getInclusions(): array
    {
        return $this->getSection(ConfigCacheWarmer::INCLUSIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function isCacheFresh(?int $timestamp): bool
    {
        if (null === $timestamp) {
            return true;
        }

        $cacheTimestamp = $this->getCacheTimestamp();
        if (null === $cacheTimestamp || $cacheTimestamp > $timestamp) {
            return false;
        }

        if (null === $this->cacheFresh) {
            $this->cacheFresh = $this->getCache()->isFresh();
        }

        return $this->cacheFresh;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTimestamp(): ?int
    {
        if (false === $this->cacheTimestamp) {
            $cacheTimestamp = null;
            $cacheFile = $this->getCache()->getPath();
            if (file_exists($cacheFile)) {
                $cacheTimestamp = filemtime($cacheFile);
                if (false === $cacheTimestamp) {
                    throw new IOException(sprintf('Cannot get modification time for "%s" file.', $cacheFile));
                }
            }
            $this->cacheTimestamp = $cacheTimestamp;
            $this->cacheFresh = null;
        }

        return $this->cacheTimestamp;
    }

    /**
     * @param string $section
     *
     * @return mixed
     */
    private function getSection(string $section)
    {
        $data = $this->getData();

        return $data[$section];
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        if (null === $this->data) {
            $cache = $this->getCache();
            $cacheFile = $cache->getPath();
            if (!$cache->isFresh()) {
                $overrideExistingCacheFile = $this->debug && file_exists($cacheFile);

                $cache->warmUpCache();
                $this->cacheTimestamp = false;
                $this->cacheFresh = null;

                if ($overrideExistingCacheFile) {
                    clearstatcache(false, $cacheFile);
                }
            }

            $data = require $cacheFile;
            if (!\is_array($data)) {
                throw new \LogicException(sprintf('The "%s" must return an array.', $cacheFile));
            }
            $this->data = $data;
        }

        return $this->data;
    }

    /**
     * @return ConfigCacheFile
     */
    private function getCache(): ConfigCacheFile
    {
        if (null === $this->cache) {
            $this->cache = $this->configCacheFactory->getCache($this->configKey);
        }

        return $this->cache;
    }
}
