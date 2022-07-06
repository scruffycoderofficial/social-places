<?php

namespace Oro\Bundle\EntityConfigBundle\ImportExport\Processor;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Oro\Bundle\EntityConfigBundle\Entity\FieldConfigModel;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;
use Oro\Bundle\ImportExportBundle\Processor\ImportProcessor;

/**
 * Import processor for attributes should be used without RegistryDelegateProcessor registry to avoid it usage
 * for a simple FieldConfigModel.
 */
class AttributeImportProcessor extends ImportProcessor implements StepExecutionAwareInterface
{
    /**
     * @var ContextRegistry
     */
    protected $contextRegistry;

    /**
     * @var StepExecution
     */
    protected $stepExecution;

    /**
     * {@inheritdoc}
     */
    public function process($item)
    {
        $context = $this->contextRegistry->getByStepExecution($this->stepExecution);
        $this->setImportExportContext($context);
        $this->setEntityName(FieldConfigModel::class);

        return parent::process($item);
    }

    /**
     * @param ContextRegistry $contextRegistry
     */
    public function setContextRegistry(ContextRegistry $contextRegistry)
    {
        $this->contextRegistry = $contextRegistry;
    }

    /**
     * @param StepExecution $stepExecution
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }
}
