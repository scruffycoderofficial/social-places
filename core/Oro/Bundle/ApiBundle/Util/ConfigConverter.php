<?php

namespace Oro\Bundle\ApiBundle\Util;

use Oro\Bundle\ApiBundle\Provider\EntityOverrideProviderRegistry;
use Oro\Bundle\ApiBundle\Request\RequestType;
use Oro\Component\EntitySerializer\AssociationQuery;
use Oro\Component\EntitySerializer\ConfigConverter as BaseConfigConverter;
use Oro\Component\EntitySerializer\EntityConfig;
use Oro\Component\EntitySerializer\FieldConfig;

/**
 * Provides a method to convert normalized configuration of the EntityConfig object.
 */
class ConfigConverter extends BaseConfigConverter
{
    /** @var EntityOverrideProviderRegistry */
    private $entityOverrideProviderRegistry;

    /** @var RequestType|null */
    private $requestType;

    /**
     * @param EntityOverrideProviderRegistry $entityOverrideProviderRegistry
     */
    public function __construct(EntityOverrideProviderRegistry $entityOverrideProviderRegistry)
    {
        $this->entityOverrideProviderRegistry = $entityOverrideProviderRegistry;
    }

    /**
     * @param RequestType|null $requestType
     */
    public function setRequestType(RequestType $requestType = null): void
    {
        $this->requestType = $requestType;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildEntityConfig(EntityConfig $result, array $config)
    {
        parent::buildEntityConfig($result, $config);

        if (!empty($config[ConfigUtil::PARENT_RESOURCE_CLASS])) {
            $result->set(AclProtectedQueryResolver::SKIP_ACL_FOR_ROOT_ENTITY, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setAssociationQuery(FieldConfig $result, array $config)
    {
        if (isset($config[ConfigUtil::ASSOCIATION_QUERY])) {
            $result->set(
                ConfigUtil::ASSOCIATION_QUERY,
                new AssociationQuery(
                    $config[ConfigUtil::ASSOCIATION_QUERY],
                    $this->getEntityClass($config[ConfigUtil::TARGET_CLASS])
                )
            );
        }
    }

    /**
     * @param string $class
     *
     * @return string
     */
    protected function getEntityClass(string $class): string
    {
        if (null === $this->requestType) {
            return $class;
        }

        $entityClass = $this->entityOverrideProviderRegistry
            ->getEntityOverrideProvider($this->requestType)
            ->getEntityClass($class);
        if ($entityClass) {
            return $entityClass;
        }

        return $class;
    }
}
