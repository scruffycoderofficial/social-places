<?php

namespace Oro\Bundle\OrganizationBundle\Twig;

use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\OrganizationBundle\Entity\Manager\BusinessUnitManager;
use Oro\Bundle\SecurityBundle\Owner\EntityOwnerAccessor;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Acl\Util\ClassUtils;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides Twig functions to deal with entity owners:
 *   - oro_get_owner_type
 *   - oro_get_entity_owner
 *   - oro_get_owner_field_name
 *   - oro_get_business_units_count
 */
class OrganizationExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    const EXTENSION_NAME = 'oro_owner_type';

    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return ConfigManager
     */
    protected function getConfigManager()
    {
        return $this->container->get(ConfigManager::class);
    }

    /**
     * @return EntityOwnerAccessor
     */
    protected function getOwnerAccessor()
    {
        return $this->container->get(EntityOwnerAccessor::class);
    }

    /**
     * @return BusinessUnitManager
     */
    protected function getBusinessUnitManager()
    {
        return $this->container->get(BusinessUnitManager::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('oro_get_owner_type', [$this, 'getOwnerType']),
            new TwigFunction('oro_get_entity_owner', [$this, 'getEntityOwner']),
            new TwigFunction('oro_get_owner_field_name', [$this, 'getOwnerFieldName']),
            new TwigFunction('oro_get_business_units_count', [$this, 'getBusinessUnitCount']),
        ];
    }

    /**
     * @param object $entity
     *
     * @return string
     */
    public function getOwnerType($entity)
    {
        $ownerClassName = ClassUtils::getRealClass($entity);

        $configManager = $this->getConfigManager();
        if (!$configManager->hasConfig($ownerClassName)) {
            return null;
        }

        return $configManager->getEntityConfig('ownership', $ownerClassName)->get('owner_type');
    }

    /**
     * @param object $entity
     *
     * @return string
     */
    public function getOwnerFieldName($entity)
    {
        $ownerClassName = ClassUtils::getRealClass($entity);

        $configManager = $this->getConfigManager();
        if (!$configManager->hasConfig($ownerClassName)) {
            return null;
        }

        return $configManager->getEntityConfig('ownership', $ownerClassName)->get('owner_field_name');
    }

    /**
     * @param object $entity
     *
     * @return null|object
     */
    public function getEntityOwner($entity)
    {
        return $this->getOwnerAccessor()->getOwner($entity);
    }

    /**
     * @return int
     */
    public function getBusinessUnitCount()
    {
        return $this->getBusinessUnitManager()->getBusinessUnitRepo()->getBusinessUnitsCount();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::EXTENSION_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return [
            ConfigManager::class,
            EntityOwnerAccessor::class,
            BusinessUnitManager::class,
        ];
    }
}
