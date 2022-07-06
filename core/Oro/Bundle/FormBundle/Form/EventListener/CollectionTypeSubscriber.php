<?php

namespace Oro\Bundle\FormBundle\Form\EventListener;

use Doctrine\Common\Collections\Collection;
use Oro\Bundle\FormBundle\Entity\EmptyItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Removes empty collection elements and sets the first non-empty item as primary.
 */
class CollectionTypeSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'postSubmit',
            FormEvents::PRE_SUBMIT  => 'preSubmit'
        );
    }

    /**
     * Removes empty collection elements.
     *
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        /** @var Collection $items */
        $items = $event->getData();

        if (!$items || !$items instanceof Collection) {
            return;
        }

        foreach ($items as $item) {
            if ($item instanceof EmptyItem && $item->isEmpty()) {
                $items->removeElement($item);
            }
        }
    }

    /**
     * Remove empty items to prevent validation.
     *
     * @param FormEvent $event
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function preSubmit(FormEvent $event)
    {
        $items = $event->getData();

        if (!$items || !is_array($items)) {
            return;
        }

        if (!$this->hasPrimaryBehaviour($event)) {
            return;
        }

        $notEmptyItems = array();
        $hasPrimary = false;

        // Remove empty items
        foreach ($items as $index => $item) {
            if (!$this->isArrayEmpty($item)) {
                $hasPrimary = $hasPrimary || (array_key_exists('primary', $item) && $item['primary']);
                $notEmptyItems[$index] = $item;
            }
        }

        $items = $notEmptyItems;

        // Set first non empty item for new item as primary
        if ($items && !$hasPrimary && count($items) == 1) {
            $items[current(array_keys($items))]['primary'] = true;
        }

        $event->setData($items);
    }

    /**
     * @param FormEvent $event
     *
     * @return bool
     */
    protected function hasPrimaryBehaviour(FormEvent $event)
    {
        if (!$event->getForm()->getConfig()->getOption('handle_primary')) {
            return false;
        }
        /** @var FormInterface $child */
        foreach ($event->getForm() as $child) {
            $dataClass = $child->getConfig()->getDataClass();
            if ($dataClass && !in_array('Oro\\Bundle\\FormBundle\\Entity\\PrimaryItem', class_implements($dataClass))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if array is empty
     *
     * @param array $array
     * @return bool
     */
    protected function isArrayEmpty($array)
    {
        foreach ($array as $val) {
            if (is_array($val)) {
                if (!$this->isArrayEmpty($val)) {
                    return false;
                }
            } elseif (!empty($val)) {
                return false;
            }
        }
        return true;
    }
}
