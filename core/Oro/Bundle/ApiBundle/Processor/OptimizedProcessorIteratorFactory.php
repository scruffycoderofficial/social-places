<?php

namespace Oro\Bundle\ApiBundle\Processor;

use Oro\Component\ChainProcessor\ApplicableCheckerInterface;
use Oro\Component\ChainProcessor\ContextInterface as ComponentContextInterface;
use Oro\Component\ChainProcessor\ProcessorBagAwareIteratorFactoryInterface;
use Oro\Component\ChainProcessor\ProcessorBagInterface;
use Oro\Component\ChainProcessor\ProcessorIteratorFactoryInterface;
use Oro\Component\ChainProcessor\ProcessorRegistryInterface;

/**
 * The factory to create an instance of OptimizedProcessorIterator class.
 */
class OptimizedProcessorIteratorFactory implements
    ProcessorIteratorFactoryInterface,
    ProcessorBagAwareIteratorFactoryInterface
{
    /** @var ProcessorBagInterface|null */
    private $processorBag;

    /** @var array [action => [[processor id, [attribute name => attribute value, ...]], ...], ...] */
    private $processors;

    /** @var array [action => [group name => group index, ...], ...] */
    private $groups;

    /** @var array [action => [processor index => group name, ...], ...] */
    private $processorGroups;

    /**
     * {@inheritdoc}
     */
    public function createProcessorIterator(
        array $processors,
        ComponentContextInterface $context,
        ApplicableCheckerInterface $applicableChecker,
        ProcessorRegistryInterface $processorRegistry
    ) {
        $action = $context->getAction();
        if (!isset($this->processors[$action])) {
            $this->processors[$action] = $processors;
            $this->initializeProcessors($action);
            $this->groups[$action] = $this->loadGroups($this->processorBag->getActionGroups($action));
        }

        return new OptimizedProcessorIterator(
            $this->processors[$action],
            $this->groups[$action],
            $this->processorGroups[$action],
            $context,
            $applicableChecker,
            $processorRegistry
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setProcessorBag(ProcessorBagInterface $processorBag = null)
    {
        $this->processorBag = $processorBag;
        $this->processors = [];
        $this->groups = [];
        $this->processorGroups = [];
    }

    /**
     * @param string[] $groups
     *
     * @return array [group name => group index, ...]
     */
    private function loadGroups(array $groups): array
    {
        $result = [];
        $groupIndex = 0;
        foreach ($groups as $group) {
            $result[$group] = $groupIndex;
            $groupIndex++;
        }

        return $result;
    }

    /**
     * @param string $action
     */
    private function initializeProcessors(string $action): void
    {
        $processors = $this->processors[$action];
        $processorGroups = [];
        foreach ($processors as $index => $item) {
            $attributes = $item[1];
            if (\array_key_exists('group', $attributes)) {
                $processorGroups[$index] = $attributes['group'];
                unset($attributes['group']);
                $processors[$index][1] = $attributes;
            } else {
                $processorGroups[$index] = null;
            }
        }
        $this->processors[$action] = $processors;
        $this->processorGroups[$action] = $processorGroups;
    }
}
