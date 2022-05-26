<?php

namespace BeyondCapable\Entity\Blog\Event\Listener;

use BeyondCapable\Entity\Comment;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Class CommentInsultingFilterEventListener
 *
 * @package App\Entity\Blog\Event\Listener
 */
class CommentInsultingFilterEventListener
{
    private const INSULTING_WORDS = ['connard', 'lenancker'];

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Comment) {
            $entity->setBody(str_ireplace(self::INSULTING_WORDS, 'censored', $entity->getBody()));
        }
    }
}
