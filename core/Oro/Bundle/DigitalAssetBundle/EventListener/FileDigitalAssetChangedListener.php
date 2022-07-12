<?php

namespace Oro\Bundle\DigitalAssetBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\DigitalAssetBundle\Entity\DigitalAsset;
use Oro\Bundle\DigitalAssetBundle\Reflector\FileReflector;

/**
 * Listens to changes of File::$digitalAsset relation to update File according to DigitalAsset::$sourceFile.
 */
class FileDigitalAssetChangedListener
{
    /** @var FileReflector */
    private $fileReflector;

    /**
     * @param FileReflector $fileReflector
     */
    public function __construct(FileReflector $fileReflector)
    {
        $this->fileReflector = $fileReflector;
    }

    /**
     * @param File $file
     * @param LifecycleEventArgs $args
     */
    public function prePersist(File $file, LifecycleEventArgs $args): void
    {
        /** @var DigitalAsset|null $digitalAsset */
        $digitalAsset = $file->getDigitalAsset();
        if (!$digitalAsset || !$digitalAsset->getId()) {
            return;
        }

        $this->fileReflector->reflectFromDigitalAsset($file, $digitalAsset);
    }

    /**
     * Reflects files from new digital assets which are yet going to be persisted.
     * Covers case when both child file and digital asset are new, but child file entity gets persisted before the
     * digital asset entity.
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $entityManager = $args->getEntityManager();
        if (!$entityManager->getMetadataFactory()->hasMetadataFor(File::class)) {
            return;
        }

        $fileClassMetadata = $entityManager->getClassMetadata(File::class);
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if (!$entity instanceof File || !$entity->getDigitalAsset() || $entity->getDigitalAsset()->getId()) {
                continue;
            }

            $this->fileReflector->reflectFromDigitalAsset($entity, $entity->getDigitalAsset());

            $unitOfWork->recomputeSingleEntityChangeSet($fileClassMetadata, $entity);
        }
    }

    /**
     * @param File $entity
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(File $entity, LifecycleEventArgs $args): void
    {
        $changeSet = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
        if (!empty($changeSet['digitalAsset'][1])) {
            $this->prePersist($entity, $args);
        }
    }
}
