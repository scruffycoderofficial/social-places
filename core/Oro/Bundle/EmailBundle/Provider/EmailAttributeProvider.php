<?php

namespace Oro\Bundle\EmailBundle\Provider;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Oro\Bundle\EmailBundle\Entity\EmailInterface;
use Oro\Bundle\EmailBundle\Model\EmailAttribute;
use Oro\Bundle\EmailBundle\Model\EmailHolderInterface;
use Oro\Bundle\EmailBundle\Tools\EmailAddressHelper;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\LocaleBundle\Formatter\NameFormatter;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Contains methods related to email attributes
 */
class EmailAttributeProvider
{
    /** @var Registry */
    private $registry;

    /** @var ConfigManager */
    private $configManager;

    /** @var NameFormatter */
    private $nameFormatter;

    /** @var EmailAddressHelper */
    private $emailAddressHelper;

    /** @var PropertyAccessor */
    private $propertyAccessor;

    /**
     * @param Registry $registry
     * @param ConfigManager $configManager
     * @param NameFormatter $nameFormatter
     * @param EmailAddressHelper $emailAddressHelper
     */
    public function __construct(
        Registry $registry,
        ConfigManager $configManager,
        NameFormatter $nameFormatter,
        EmailAddressHelper $emailAddressHelper
    ) {
        $this->registry = $registry;
        $this->configManager = $configManager;
        $this->nameFormatter = $nameFormatter;
        $this->emailAddressHelper = $emailAddressHelper;
    }

    /**
     * @param string $className
     *
     * @return array
     */
    public function getAttributes($className)
    {
        $attributes = [];
        $metadata = $this->getMetadata($className);

        if (is_a($className, EmailHolderInterface::class, true)) {
            $attributes[] = new EmailAttribute('email');
        }
        $attributes = array_merge($attributes, $this->getFieldAttributes($metadata));

        return $attributes;
    }

    /**
     * @param EmailAttribute[] $attributes
     * @param object $object
     *
     * @return array
     */
    public function createEmailsFromAttributes(array $attributes, $object)
    {
        $emails = [];

        foreach ($attributes as $attribute) {
            try {
                $value = $this->getPropertyAccessor()->getValue($object, $attribute->getName());
            } catch (NoSuchPropertyException $e) {
                $value = null;
            }

            if (!$value instanceof \Traversable) {
                $value = [$value];
            }

            foreach ($value as $email) {
                if (is_scalar($email)) {
                    $emails[$email] = $this->formatEmail($object, $email);
                } elseif ($email instanceof EmailInterface) {
                    $emails[$email->getEmail()] = $this->formatEmail($email->getEmailOwner(), $email->getEmail());
                }
            }
        }

        return $emails;
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return EmailAttribute[]
     */
    private function getFieldAttributes(ClassMetadata $metadata)
    {
        $attributes = [];
        foreach ($metadata->fieldNames as $fieldName) {
            if (!$this->configManager->hasConfig($metadata->name, $fieldName)) {
                continue;
            }

            if ($this->configManager->isHiddenModel($metadata->name, $fieldName)) {
                continue;
            }

            if (false !== stripos($fieldName, 'email')) {
                $extendFieldConfig = $this->configManager->getFieldConfig('extend', $metadata->name, $fieldName);
                if (!$extendFieldConfig->is('is_deleted')) {
                    $attributes[] = new EmailAttribute($fieldName);
                }
                continue;
            }

            $entityFieldConfig = $this->configManager->getFieldConfig('entity', $metadata->name, $fieldName);
            if ($entityFieldConfig->get('contact_information') === 'email') {
                $attributes[] = new EmailAttribute($fieldName);
            }
        }

        return $attributes;
    }

    /**
     * @param string $className
     *
     * @return ClassMetadata
     */
    private function getMetadata($className)
    {
        $em = $this->registry->getManagerForClass($className);

        return $em->getClassMetadata($className);
    }

    /**
     * @param object|null $owner
     * @param string $email
     *
     * @return string
     */
    private function formatEmail($owner, $email)
    {
        if (!$owner) {
            return $email;
        }

        $ownerName = $this->nameFormatter->format($owner);

        return $this->emailAddressHelper->buildFullEmailAddress($email, $ownerName);
    }

    /**
     * @return PropertyAccessor
     */
    private function getPropertyAccessor()
    {
        if (!$this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
