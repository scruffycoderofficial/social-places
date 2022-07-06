<?php

namespace Oro\Bundle\ApiBundle\Filter;

use Oro\Bundle\SearchBundle\Provider\AbstractSearchMappingProvider;

/**
 * The factory to create SearchFieldResolver.
 */
class SearchFieldResolverFactory
{
    /** @var AbstractSearchMappingProvider */
    private $searchMappingProvider;

    /**
     * @param AbstractSearchMappingProvider $searchMappingProvider
     */
    public function __construct(AbstractSearchMappingProvider $searchMappingProvider)
    {
        $this->searchMappingProvider = $searchMappingProvider;
    }

    /**
     * Creates a new instance of SearchFieldResolver.
     *
     * @param string $entityClass
     * @param array  $fieldMappings [field name => field name in search index, ...]
     *
     * @return SearchFieldResolver
     */
    public function createFieldResolver(string $entityClass, array $fieldMappings): SearchFieldResolver
    {
        return new SearchFieldResolver(
            $this->getSearchFieldMappings($entityClass),
            $fieldMappings
        );
    }

    /**
     * @param string $entityClass
     *
     * @return array
     */
    protected function getSearchFieldMappings(string $entityClass): array
    {
        $mapping = $this->searchMappingProvider->getEntityConfig($entityClass);

        return $mapping['fields'];
    }
}
