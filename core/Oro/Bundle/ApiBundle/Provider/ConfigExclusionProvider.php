<?php

namespace Oro\Bundle\ApiBundle\Provider;

use Doctrine\ORM\Mapping\ClassMetadata;
use Oro\Bundle\EntityBundle\Provider\ChainExclusionProvider;
use Oro\Bundle\EntityBundle\Provider\EntityHierarchyProviderInterface;
use Oro\Bundle\EntityBundle\Provider\EntityRuleMatcher;

/**
 * The exclusion provider for entities and fields excluded from API
 * via "Resources/config/oro/api.yml" files.
 */
class ConfigExclusionProvider extends ChainExclusionProvider
{
    /** @var EntityHierarchyProviderInterface */
    private $entityHierarchyProvider;

    /** @var ConfigCache */
    private $configCache;

    /** @var EntityRuleMatcher */
    private $excludeMatcher;

    /** @var EntityRuleMatcher */
    private $includeMatcher;

    /** @var array */
    private $cache = [];

    /**
     * @param EntityHierarchyProviderInterface $entityHierarchyProvider
     * @param ConfigCache                      $configCache
     */
    public function __construct(
        EntityHierarchyProviderInterface $entityHierarchyProvider,
        ConfigCache $configCache
    ) {
        $this->entityHierarchyProvider = $entityHierarchyProvider;
        $this->configCache = $configCache;
    }

    /**
     * {@inheritdoc}
     */
    public function isIgnoredEntity($className)
    {
        if ($this->getIncludeMatcher()->isEntityMatched($className)) {
            return false;
        }
        if ($this->getExcludeMatcher()->isEntityMatched($className)) {
            return true;
        }

        return parent::isIgnoredEntity($className);
    }

    /**
     * {@inheritdoc}
     */
    public function isIgnoredField(ClassMetadata $metadata, $fieldName)
    {
        if (isset($this->cache[$metadata->name][$fieldName])) {
            return $this->cache[$metadata->name][$fieldName];
        }

        $result = false;
        if (!$this->getIncludeMatcher()->isFieldMatched($metadata->name, $fieldName)) {
            $result = $this->getExcludeMatcher()->isFieldMatched($metadata->name, $fieldName)
                || parent::isIgnoredField($metadata, $fieldName);
        }

        $this->cache[$metadata->name][$fieldName] = $result;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isIgnoredRelation(ClassMetadata $metadata, $associationName)
    {
        if (isset($this->cache[$metadata->name][$associationName])) {
            return $this->cache[$metadata->name][$associationName];
        }

        $result = false;
        if (!$this->getIncludeMatcher()->isFieldMatched($metadata->name, $associationName)) {
            $result = $this->getExcludeMatcher()->isFieldMatched($metadata->name, $associationName)
                || parent::isIgnoredRelation($metadata, $associationName);
        }

        $this->cache[$metadata->name][$associationName] = $result;

        return $result;
    }

    /**
     * @return EntityRuleMatcher
     */
    private function getExcludeMatcher(): EntityRuleMatcher
    {
        if (null === $this->excludeMatcher) {
            $this->excludeMatcher = new EntityRuleMatcher(
                $this->entityHierarchyProvider,
                $this->configCache->getExclusions()
            );
        }

        return $this->excludeMatcher;
    }

    /**
     * @return EntityRuleMatcher
     */
    private function getIncludeMatcher(): EntityRuleMatcher
    {
        if (null === $this->includeMatcher) {
            $this->includeMatcher = new EntityRuleMatcher(
                $this->entityHierarchyProvider,
                $this->configCache->getInclusions()
            );
        }

        return $this->includeMatcher;
    }
}
