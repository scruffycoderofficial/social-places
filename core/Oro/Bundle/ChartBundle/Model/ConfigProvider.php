<?php

namespace Oro\Bundle\ChartBundle\Model;

use Oro\Bundle\ChartBundle\Exception\InvalidConfigurationException;
use Oro\Component\Config\Cache\PhpArrayConfigProvider;
use Oro\Component\Config\Loader\CumulativeConfigLoader;
use Oro\Component\Config\Loader\CumulativeConfigProcessorUtil;
use Oro\Component\Config\Loader\YamlCumulativeFileLoader;
use Oro\Component\Config\ResourcesContainerInterface;

/**
 * The provider for charts configuration
 * that is loaded from "Resources/config/oro/charts.yml" files.
 */
class ConfigProvider extends PhpArrayConfigProvider
{
    private const CONFIG_FILE = 'Resources/config/oro/charts.yml';

    /**
     * @return string[]
     */
    public function getChartNames(): array
    {
        return array_keys($this->doGetConfig());
    }

    /**
     * @param string $chartName
     *
     * @return bool
     */
    public function hasChartConfig(string $chartName): bool
    {
        $config = $this->doGetConfig();

        return isset($config[$chartName]);
    }

    /**
     * @param string $chartName
     *
     * @return array
     *
     * @throws InvalidConfigurationException
     */
    public function getChartConfig(string $chartName): array
    {
        $config = $this->doGetConfig();
        if (!isset($config[$chartName])) {
            throw new InvalidConfigurationException($chartName);
        }

        return $config[$chartName];
    }

    /**
     * {@inheritdoc}
     */
    protected function doLoadConfig(ResourcesContainerInterface $resourcesContainer)
    {
        $configs = [];
        $configLoader = new CumulativeConfigLoader(
            'oro_chart',
            new YamlCumulativeFileLoader(self::CONFIG_FILE)
        );
        $resources = $configLoader->load($resourcesContainer);
        foreach ($resources as $resource) {
            if (!empty($resource->data[Configuration::ROOT_NODE_NAME])) {
                $configs[] = $resource->data[Configuration::ROOT_NODE_NAME];
            }
        }

        return CumulativeConfigProcessorUtil::processConfiguration(
            self::CONFIG_FILE,
            new Configuration(),
            [\array_replace_recursive(...$configs)]
        );
    }
}
