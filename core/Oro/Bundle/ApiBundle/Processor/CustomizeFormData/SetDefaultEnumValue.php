<?php

namespace Oro\Bundle\ApiBundle\Processor\CustomizeFormData;

use Oro\Bundle\ApiBundle\Util\DoctrineHelper;
use Oro\Bundle\EntityExtendBundle\Provider\EnumValueProvider;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Sets a default value for a specific enum field for a new entity
 * if a default value exists and another value is not set for the entity.
 */
class SetDefaultEnumValue implements ProcessorInterface
{
    /** @var EnumValueProvider */
    private $enumValueProvider;

    /** @var DoctrineHelper */
    private $doctrineHelper;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    /** @var string */
    private $enumFieldName;

    /** @var string */
    private $enumCode;

    /**
     * @param EnumValueProvider         $enumValueProvider
     * @param DoctrineHelper            $doctrineHelper
     * @param PropertyAccessorInterface $propertyAccessor
     * @param string                    $enumFieldName
     * @param string                    $enumCode
     */
    public function __construct(
        EnumValueProvider $enumValueProvider,
        DoctrineHelper $doctrineHelper,
        PropertyAccessorInterface $propertyAccessor,
        string $enumFieldName,
        string $enumCode
    ) {
        $this->enumValueProvider = $enumValueProvider;
        $this->doctrineHelper = $doctrineHelper;
        $this->propertyAccessor = $propertyAccessor;
        $this->enumFieldName = $enumFieldName;
        $this->enumCode = $enumCode;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        /** @var CustomizeFormDataContext $context */

        $enumFieldForm = $context->findFormField($this->enumFieldName);
        if (null === $enumFieldForm || $enumFieldForm->isSubmitted()) {
            return;
        }

        $entity = $context->getData();
        if (!$this->doctrineHelper->isNewEntity($entity)) {
            return;
        }

        if (null !== $this->propertyAccessor->getValue($entity, $this->enumFieldName)) {
            return;
        }

        $defaultValue = $this->enumValueProvider->getDefaultEnumValueByCode($this->enumCode);
        if (null !== $defaultValue) {
            $this->propertyAccessor->setValue($entity, $this->enumFieldName, $defaultValue);
        }
    }
}
