<?php

namespace Oro\Bundle\SecurityBundle\Filter;

use Oro\Bundle\EntityConfigBundle\Config\ConfigInterface;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Component\EntitySerializer\DoctrineHelper;
use Oro\Component\EntitySerializer\FieldFilterInterface;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Checks the availability of a field based on the entity configuration and VIEW permission of the field.
 */
class EntitySerializerFieldFilter implements FieldFilterInterface
{
    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var DoctrineHelper */
    private $doctrineHelper;

    /** @var ConfigManager */
    private $configManager;

    /** @var bool TRUE if access to entity ID field should be checked */
    private $isIdFieldProtected;

    /** @var ConfigInterface[] */
    private $securityConfigs = [];

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ConfigManager                 $configManager
     * @param DoctrineHelper                $doctrineHelper
     * @param bool                          $isIdFieldProtected
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ConfigManager $configManager,
        DoctrineHelper $doctrineHelper,
        bool $isIdFieldProtected = true
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->configManager = $configManager;
        $this->doctrineHelper = $doctrineHelper;
        $this->isIdFieldProtected = $isIdFieldProtected;
    }

    /**
     * {@inheritdoc}
     */
    public function checkField($entity, string $entityClass, string $field): ?bool
    {
        if (!$this->isIdFieldProtected && $this->isIdentifierField($entityClass, $field)) {
            return null;
        }

        $securityConfig = $this->getSecurityConfig($entityClass);
        if (null === $securityConfig) {
            return null;
        }

        if (!$securityConfig->is('field_acl_supported') || !$securityConfig->is('field_acl_enabled')) {
            return null;
        }

        if ($this->authorizationChecker->isGranted('VIEW', new FieldVote($entity, $field))) {
            return null;
        }

        return !$securityConfig->get('show_restricted_fields', false, true);
    }

    /**
     * @param string $entityClass
     * @param string $field
     *
     * @return bool
     */
    private function isIdentifierField(string $entityClass, string $field): bool
    {
        return $this->doctrineHelper->getEntityIdFieldName($entityClass) === $field;
    }

    /**
     * @param string $entityClass
     *
     * @return ConfigInterface|null
     */
    private function getSecurityConfig(string $entityClass): ?ConfigInterface
    {
        if (\array_key_exists($entityClass, $this->securityConfigs)) {
            return $this->securityConfigs[$entityClass];
        }

        $securityConfig = $this->configManager->hasConfig($entityClass)
            ? $this->configManager->getEntityConfig('security', $entityClass)
            : null;
        $this->securityConfigs[$entityClass] = $securityConfig;

        return $securityConfig;
    }
}
