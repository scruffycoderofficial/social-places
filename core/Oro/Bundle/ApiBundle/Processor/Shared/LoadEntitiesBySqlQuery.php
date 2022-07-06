<?php

namespace Oro\Bundle\ApiBundle\Processor\Shared;

use Oro\Bundle\ApiBundle\Processor\ListContext;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Component\DoctrineUtils\ORM\SqlQuery;
use Oro\Component\DoctrineUtils\ORM\SqlQueryBuilder;

/**
 * Loads entities using SqlQueryBuilder or SqlQuery object.
 * IMPORTANT: this processor does not apply access rules to the query.
 */
class LoadEntitiesBySqlQuery implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        /** @var ListContext $context */

        if ($context->hasResult()) {
            // result data are already retrieved
            return;
        }

        $query = $context->getQuery();
        if ($query instanceof SqlQueryBuilder) {
            $context->setResult($query->getQuery()->getResult());
        } elseif ($query instanceof SqlQuery) {
            $context->setResult($query->getResult());
        }
    }
}
