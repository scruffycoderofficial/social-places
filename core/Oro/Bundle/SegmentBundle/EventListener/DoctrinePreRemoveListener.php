<?php

namespace Oro\Bundle\SegmentBundle\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\SegmentBundle\Entity\SegmentSnapshot;

/**
 * Removes records from segment snapshot when referenced entity is removed.
 */
class DoctrinePreRemoveListener
{
    /** @var ConfigManager */
    private $configManager;

    /** @var array */
    private $deleteEntities = [];

    /**
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $className = ClassUtils::getClass($entity);

        if ($this->configManager->hasConfig($className)) {
            $metadata  = $args->getEntityManager()->getClassMetadata($className);
            $entityIds = $metadata->getIdentifierValues($entity);
            $this->deleteEntities[] = [
                'id'     => reset($entityIds),
                'entity' => $entity
            ];
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->deleteEntities) {
            $em = $args->getEntityManager();
            $knownNamespaces = $em->getConfiguration()->getEntityNamespaces();
            if (!empty($knownNamespaces['OroSegmentBundle'])) {
                $em->getRepository(SegmentSnapshot::class)->massRemoveByEntities($this->deleteEntities);
                $this->deleteEntities = [];
            }
        }
    }
}
