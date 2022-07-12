<?php

namespace Oro\Bundle\SecurityBundle\Owner;

use Doctrine\Common\Cache\CacheProvider;
use Oro\Bundle\EntityBundle\Tools\DatabaseChecker;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * The base class for owner tree providers.
 */
abstract class AbstractOwnerTreeProvider implements OwnerTreeProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const CACHE_KEY = 'data';

    /** @var DatabaseChecker */
    private $databaseChecker;

    /** @var CacheProvider */
    protected $cache;

    /**
     * @param DatabaseChecker $databaseChecker
     * @param CacheProvider   $cache
     */
    public function __construct(DatabaseChecker $databaseChecker, CacheProvider $cache)
    {
        $this->databaseChecker = $databaseChecker;
        $this->cache = $cache;
    }

    /**
     * @param OwnerTreeBuilderInterface $tree
     */
    abstract protected function fillTree(OwnerTreeBuilderInterface $tree): void;

    /**
     * Returns empty instance of the owner tree builder
     *
     * @return OwnerTreeBuilderInterface
     */
    protected function createTreeBuilder(): OwnerTreeBuilderInterface
    {
        return new OwnerTree();
    }

    /**
     * Calculate subordinated entity ids.
     */
    protected function buildTree(array $businessUnitIds, $businetUnitClass)
    {
        $subordinateBusinessUnitIds = [];
        $calculatedLevels = array_reverse($this->calculateAdjacencyListLevels($businessUnitIds, $businetUnitClass));
        foreach ($calculatedLevels as $businessUnitIdss) {
            foreach ($businessUnitIdss as $parentBuId => $buId) {
                $parentBuId = $businessUnitIds[$buId];
                if (null !== $parentBuId) {
                    $subordinateBusinessUnitIds[$parentBuId][] = $buId;
                    if (isset($subordinateBusinessUnitIds[$buId])) {
                        $subordinateBusinessUnitIds[$parentBuId] = array_merge(
                            $subordinateBusinessUnitIds[$parentBuId],
                            $subordinateBusinessUnitIds[$buId]
                        );
                    }
                }
            }
        }

        return $subordinateBusinessUnitIds;
    }

    /**
     * Takes business units adjacency list and calculates tree level for each item in list.
     *
     * For details about Adjacency Lists see https://en.wikipedia.org/wiki/Adjacency_list
     * The performance of the implemented algorithm depends on the order of items in the input list.
     * The best performance is reached when all children are added to the input list after parents.
     *
     * An example:
     *
     *  id    -  parentID          Tree                        id    -  parentID  - level
     * ------------------       --------------------           ----------------------------
     *  b1    -  null              b1                          b1    -  null         0
     *  b2    -  null               +-- b11                    b2    -  null         0
     *  b11   -  b1                 |   +-- b111               b11   -  b1           1
     *  b12   -  b1                 |       +-- b1111          b12   -  b1           1
     *  b21   -  b2                 |       +-- b1112          b21   -  b2           1
     *  b111  -  b11                +-- b12                    b111  -  b11          2
     *  b121  -  b12                    +-- b121               b121  -  b12          2
     *  b122  -  b12                    +-- b122               b122  -  b12          2
     *  b1111 -  b111                       +-- b1221          b1111 -  b111         3
     *  b1112 -  b111              b2                          b1112 -  b111         3
     *  b1221 -  b122               +-- b21                    b1221 -  b122         3
     *
     * @return array [level => [business unit id, ...], ...]
     */
    protected function calculateAdjacencyListLevels($businessUnits, $businessUnitClass)
    {
        $levelsData = [];
        while (!empty($businessUnits)) {
            $unprocessed = [];
            foreach ($businessUnits as $buId => $parentBuId) {
                if (null === $parentBuId) {
                    $levelsData[$buId] = 0;
                } elseif (array_key_exists($parentBuId, $levelsData)) {
                    $levelsData[$buId] = $levelsData[$parentBuId] + 1;
                } elseif (array_key_exists($parentBuId, $businessUnits)) {
                    $unprocessed[$buId] = $parentBuId;
                    if ($businessUnits[$parentBuId] === $buId
                        || in_array($buId, $businessUnits, true)
                    ) {
                        $this->logger->critical(
                            sprintf(
                                'Cyclic relationship in "%s" with problem id "%s"',
                                $businessUnitClass,
                                $buId
                            )
                        );
                        unset($businessUnits[$buId]);
                    }
                }
            }
            $businessUnits = $unprocessed;
        }

        $result = [];
        foreach ($levelsData as $buId => $level) {
            $result[$level][] = $buId;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function clearCache(): void
    {
        $this->cache->deleteAll();
    }

    /**
     * {@inheritdoc}
     */
    public function warmUpCache(): void
    {
        $this->cache->save(self::CACHE_KEY, $this->loadTree());
    }

    /**
     * {@inheritdoc}
     */
    public function getTree(): OwnerTreeInterface
    {
        $tree = $this->cache->fetch(self::CACHE_KEY);
        if (!$tree) {
            $tree = $this->loadTree();
            $this->cache->save(self::CACHE_KEY, $tree);
        }

        return $tree;
    }

    /**
     * Loads tree data.
     *
     * @return OwnerTreeInterface
     */
    protected function loadTree(): OwnerTreeInterface
    {
        $treeBuilder = $this->createTreeBuilder();
        if ($this->databaseChecker->checkDatabase()) {
            $this->fillTree($treeBuilder);
        }

        return $treeBuilder->getTree();
    }
}
