<?php

namespace Oro\Bundle\ApiBundle\Filter;

/**
 * The factory to create SearchAggregationFilter.
 */
class SearchAggregationFilterFactory
{
    /** @var SearchFieldResolverFactory */
    private $searchFieldResolverFactory;

    /**
     * @param SearchFieldResolverFactory $searchFieldResolverFactory
     */
    public function __construct(SearchFieldResolverFactory $searchFieldResolverFactory)
    {
        $this->searchFieldResolverFactory = $searchFieldResolverFactory;
    }

    /**
     * Creates a new instance of SearchAggregationFilter.
     *
     * @param string $dataType
     *
     * @return SearchAggregationFilter
     */
    public function createFilter(string $dataType): SearchAggregationFilter
    {
        $filter = new SearchAggregationFilter($dataType);
        $filter->setSearchFieldResolverFactory($this->searchFieldResolverFactory);
        $filter->setArrayAllowed(true);

        return $filter;
    }
}
