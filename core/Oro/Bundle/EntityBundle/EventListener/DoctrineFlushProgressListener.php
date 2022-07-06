<?php

namespace Oro\Bundle\EntityBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;

/**
 * This class listen doctrine's preFlush and postFlush events to track information about flush progress
 * preFlush event means that flush is started
 * postFlush event means that flush is finished
 */
class DoctrineFlushProgressListener
{
    /**
     * @var array
     */
    private $flushInProgressByHash = [];

    /**
     * Method allows to get information about flush progress for specific entity manager
     *
     * @param EntityManager $em
     * @return bool
     */
    public function isFlushInProgress(EntityManager $em): bool
    {
        $hash = $this->getHash($em);

        return $this->flushInProgressByHash[$hash] ?? false;
    }

    /**
     * @param PreFlushEventArgs $args
     */
    public function preFlush(PreFlushEventArgs $args): void
    {
        $this->markAsInProgress($args->getEntityManager(), true);
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->markAsInProgress($args->getEntityManager(), false);
    }

    /**
     * This method set EM flush status
     *
     * Argument $isInProgress:
     *      'true' means that flush is in progress
     *      'false' means that flush finished/not started
     *
     * @param EntityManager $em
     * @param bool $isInProgress
     */
    private function markAsInProgress(EntityManager $em, bool $isInProgress): void
    {
        $hash = $this->getHash($em);
        $this->flushInProgressByHash[$hash] = $isInProgress;
    }

    /**
     * @param EntityManager $em
     * @return string
     */
    private function getHash(EntityManager $em): string
    {
        return spl_object_hash($em);
    }
}
