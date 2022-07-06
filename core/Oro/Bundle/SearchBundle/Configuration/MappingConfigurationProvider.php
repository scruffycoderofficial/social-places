<?php

namespace Oro\Bundle\SearchBundle\Configuration;

use Oro\Component\Config\Loader\CumulativeConfigLoader;
use Oro\Component\Config\Loader\CumulativeConfigProcessorUtil;
use Oro\Component\Config\Loader\YamlCumulativeFileLoader;
use Oro\Component\Config\ResourcesContainerInterface;

/**
 * The provider for search mapping configuration
 * that is loaded from "Resources/config/oro/search.yml" files.
 */
class MappingConfigurationProvider extends MappingConfigurationProviderAbstract
{
    private const CONFIG_FILE = 'Resources/config/oro/search.yml';

    /**
     * Gets website search mapping configuration.
     *
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->doGetConfig();
    }

    /**
     * {@inheritdoc}
     */
    protected function doLoadConfig(ResourcesContainerInterface $resourcesContainer)
    {
        $configs = [];
        $configLoader = new CumulativeConfigLoader(
            'oro_search',
            new YamlCumulativeFileLoader(self::CONFIG_FILE)
        );
        $resources = $configLoader->load($resourcesContainer);
        foreach ($resources as $resource) {
            if (!empty($resource->data[MappingConfiguration::ROOT_NODE])) {
                $configs[] = $resource->data[MappingConfiguration::ROOT_NODE];
            }
        }

        return CumulativeConfigProcessorUtil::processConfiguration(
            self::CONFIG_FILE,
            new MappingConfiguration(),
            $configs
        );
    }
}
