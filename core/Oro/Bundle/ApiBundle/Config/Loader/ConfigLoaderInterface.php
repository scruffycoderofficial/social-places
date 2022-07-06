<?php

namespace Oro\Bundle\ApiBundle\Config\Loader;

/**
 * The interface for configuration section loaders.
 */
interface ConfigLoaderInterface
{
    /**
     * Loads a configuration from an array.
     *
     * @param array $config
     *
     * @return mixed
     */
    public function load(array $config);
}
