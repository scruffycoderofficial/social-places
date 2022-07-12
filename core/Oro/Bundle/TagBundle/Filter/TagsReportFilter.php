<?php

namespace Oro\Bundle\TagBundle\Filter;

use Oro\Bundle\TagBundle\Form\Type\Filter\TagsReportFilterType;

/**
 * The filter by tags for reports and segments.
 */
class TagsReportFilter extends TagsDictionaryFilter
{
    /**
     * {@inheritdoc}
     */
    protected function getFormType()
    {
        return TagsReportFilterType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClassName()
    {
        return $this->get('entityClass');
    }
}
