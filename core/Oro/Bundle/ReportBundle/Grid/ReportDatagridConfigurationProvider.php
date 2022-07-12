<?php

namespace Oro\Bundle\ReportBundle\Grid;

use Doctrine\Common\Cache\Cache;
use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Provider\ConfigurationProviderInterface;
use Oro\Bundle\QueryDesignerBundle\Exception\InvalidConfigurationException;
use Oro\Bundle\QueryDesignerBundle\Grid\BuilderAwareInterface;
use Oro\Bundle\QueryDesignerBundle\Grid\DatagridConfigurationBuilder;
use Oro\Bundle\ReportBundle\Entity\Report;

/**
 * The provider for configuration of datagrids used to show reports.
 */
class ReportDatagridConfigurationProvider implements ConfigurationProviderInterface, BuilderAwareInterface
{
    /** @var ReportDatagridConfigurationBuilder */
    private $builder;

    /** @var ManagerRegistry */
    private $doctrine;

    /** @var Cache */
    private $cache;

    /** @var string */
    private $prefixCacheKey;

    /**
     * @param ReportDatagridConfigurationBuilder $builder
     * @param ManagerRegistry                    $doctrine
     * @param Cache                              $cache
     * @param string                             $prefixCacheKey
     */
    public function __construct(
        ReportDatagridConfigurationBuilder $builder,
        ManagerRegistry $doctrine,
        Cache $cache,
        string $prefixCacheKey
    ) {
        $this->builder = $builder;
        $this->doctrine = $doctrine;
        $this->cache = $cache;
        $this->prefixCacheKey = $prefixCacheKey;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(string $gridName): bool
    {
        return $this->builder->isApplicable($gridName);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(string $gridName): DatagridConfiguration
    {
        $cacheKey = $this->prefixCacheKey . '.' . $gridName;

        $config = $this->cache->fetch($cacheKey);
        if (false === $config) {
            $config = $this->buildConfiguration($gridName);
            $this->cache->save($cacheKey, $config);
        }

        return $config;
    }

    /**
     * Check whether a report is valid or not
     *
     * @param string $gridName
     *
     * @return bool
     */
    public function isReportValid(string $gridName): bool
    {
        try {
            $this->getConfiguration($gridName);
        } catch (InvalidConfigurationException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilder(): DatagridConfigurationBuilder
    {
        return $this->builder;
    }

    /**
     * @param string $gridName
     *
     * @return DatagridConfiguration
     */
    private function buildConfiguration(string $gridName): DatagridConfiguration
    {
        $id = (int)(substr($gridName, \strlen(Report::GRID_PREFIX)));
        if (!$id) {
            throw new \RuntimeException(sprintf('The report ID not found in the "%s" grid name.', $gridName));
        }

        $report = $this->doctrine->getRepository(Report::class)->find($id);

        $this->builder->setGridName($gridName);
        $this->builder->setSource($report);

        return $this->builder->getConfiguration();
    }
}
