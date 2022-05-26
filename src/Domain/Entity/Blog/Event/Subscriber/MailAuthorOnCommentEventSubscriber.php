<?php

namespace BeyondCapable\Domain\Entity\Blog\Event\Subscriber;

use BeyondCapable\Entity\Comment;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class MailAuthorOnCommentEventSubscriber
 *
 * @package App\Entity\Blog\Event\Subscriber
 */
class MailAuthorOnCommentEventSubscriber implements EventSubscriber
{
    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents(): array
    {
        return [ Events::postPersist ];
    }

    /**
     * Mails the Post's author when a new Comment is published
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Comment) {
            if ($entity->getPost()->getAuthor() && $entity->getAuthor()) {

                // Send an email to the Post Author
            }
        }
    }
}
