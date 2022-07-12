<?php

namespace Oro\Bundle\FormBundle\Form\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English\InflectorFactory;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * This subscriber adds and removes entities from relation collections.
 */
class MultipleEntitySubscriber implements EventSubscriberInterface
{
    /** @var DoctrineHelper */
    protected $doctrineHelper;
    private Inflector $inflector;

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->inflector = (new InflectorFactory())->build();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'postSet',
            FormEvents::POST_SUBMIT   => 'postSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function postSet(FormEvent $event)
    {
        $form       = $event->getForm();
        $collection = $form->getData();
        $added      = $removed = [];

        // using array_values in order to prevent passing keys
        if ($collection instanceof PersistentCollection && $collection->isDirty()) {
            $added   = array_values($collection->getInsertDiff());
            $removed = array_values($collection->getDeleteDiff());
        } elseif ($collection instanceof ArrayCollection && $collection->count() > 0) {
            $added = array_values($collection->toArray());
        }

        $form->get('added')->setData($added);
        $form->get('removed')->setData($removed);
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $fieldName = $this->getFieldName($form);

        $added = $form->get('added')->getData();
        $removed = $form->get('removed')->getData();

        $parent = $form->getParent()->getData();
        $parentMetadata = !$parent ? null : $this->doctrineHelper->getEntityMetadata(
            ClassUtils::getClass($parent),
            false
        );

        /** @var Collection $children */
        $children = $form->getData();
        foreach ($added as $child) {
            if (null !== $parentMetadata
                && $this->isOneToManyAssociation($child, $parentMetadata, $fieldName)
            ) {
                $this->setOneToManyTargetEntity($child, $parentMetadata, $fieldName, $parent);
            }

            if (!$children->contains($child)) {
                $children->add($child);
            }
        }
        foreach ($removed as $child) {
            if (null !== $parentMetadata
                && $this->isOneToManyAssociation($child, $parentMetadata, $fieldName)
            ) {
                $this->setOneToManyTargetEntity($child, $parentMetadata, $fieldName, null);
            }
            $children->removeElement($child);
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    protected function getFieldName(FormInterface $form)
    {
        $form->getPropertyPath();
        $propertyPath = $form->getConfig()->getPropertyPath();
        if (null !== $propertyPath) {
            return (string)$propertyPath;
        }

        return $form->getName();
    }

    /**
     * @param object        $owningEntity
     * @param ClassMetadata $targetMetadata
     * @param string        $targetAssociationName
     *
     * @return bool
     */
    protected function isOneToManyAssociation(
        $owningEntity,
        $targetMetadata,
        $targetAssociationName
    ) {
        if (!$targetMetadata->hasAssociation($targetAssociationName)) {
            return false;
        }

        $targetAssociationMapping = $targetMetadata->getAssociationMapping($targetAssociationName);
        if (ClassMetadata::ONE_TO_MANY !== $targetAssociationMapping['type']
            || ClassUtils::getClass($owningEntity) !== $targetAssociationMapping['targetEntity']
        ) {
            return false;
        }

        return true;
    }
    /**
     * @param object        $owningEntity
     * @param ClassMetadata $targetMetadata
     * @param string        $targetAssociationName
     * @param object|null   $targetEntity
     */
    protected function setOneToManyTargetEntity(
        $owningEntity,
        $targetMetadata,
        $targetAssociationName,
        $targetEntity
    ) {
        $targetAssociationMapping = $targetMetadata->getAssociationMapping($targetAssociationName);
        $setter = $this->getSetterName($targetAssociationMapping['mappedBy']);
        $owningEntity->$setter($targetEntity);
    }

    /**
     * @param string $mappedBy
     *
     * @return string
     */
    protected function getSetterName($mappedBy)
    {
        return 'set' . $this->inflector->classify($mappedBy);
    }
}
