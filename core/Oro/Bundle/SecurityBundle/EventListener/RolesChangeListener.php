<?php

namespace Oro\Bundle\SecurityBundle\EventListener;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\XcacheCache;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Acl\Util\ClassUtils;

/**
 * Listener that clears the doctrine query caches if user roles list was changed.
 */
class RolesChangeListener
{
    /** @var string */
    private $rolesFieldName;

    /** @var array [class name, ...] */
    private $securityClasses = [];

    /** @var bool */
    private $isCacheOutdated = false;

    /**
     * @param string $rolesFieldName
     */
    public function __construct(string $rolesFieldName = 'roles')
    {
        $this->rolesFieldName = $rolesFieldName;
    }

    /**
     * @param string $className
     */
    public function addSupportedClass(string $className): void
    {
        $this->securityClasses[] = $className;
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        if ($this->isCacheOutdated || !$this->securityClasses) {
            return;
        }

        $cacheDriver = $args->getEntityManager()->getConfiguration()->getQueryCacheImpl();
        if (!$cacheDriver || $cacheDriver instanceof ApcCache || $cacheDriver instanceof XcacheCache) {
            return;
        }

        $uow = $args->getEntityManager()->getUnitOfWork();
        $this->isCacheOutdated = $this->checkRolesRelations($uow->getScheduledCollectionUpdates())
            || $this->checkRolesRelations($uow->getScheduledCollectionDeletions());

        if ($this->isCacheOutdated) {
            $cacheDriver->deleteAll();
        }
    }

    /**
     * @param PersistentCollection[] $collections
     *
     * @return bool
     */
    private function checkRolesRelations(array $collections): bool
    {
        foreach ($collections as $collection) {
            $entityClass = ClassUtils::getRealClass($collection->getOwner());
            if (!in_array($entityClass, $this->securityClasses, true)) {
                continue;
            }

            $associationMapping = $collection->getMapping();
            if ($associationMapping['fieldName'] === $this->rolesFieldName) {
                return true;
            }
        }

        return false;
    }
}
