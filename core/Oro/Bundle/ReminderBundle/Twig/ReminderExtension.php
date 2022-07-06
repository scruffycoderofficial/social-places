<?php

namespace Oro\Bundle\ReminderBundle\Twig;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\ReminderBundle\Entity\Reminder;
use Oro\Bundle\ReminderBundle\Model\WebSocket\MessageParamsProvider;
use Oro\Bundle\UserBundle\Entity\User;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides a Twig function to retrieve reminders data for the current user:
 *   - oro_reminder_get_requested_reminders_data
 */
class ReminderExtension extends AbstractExtension implements ServiceSubscriberInterface
{
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
     * @return TokenStorageInterface
     */
    protected function getSecurityTokenStorage()
    {
        return $this->container->get('security.token_storage');
    }

    /**
     * @return MessageParamsProvider
     */
    protected function getMessageParamsProvider()
    {
        return $this->container->get('oro_reminder.web_socket.message_params_provider');
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->container->get('doctrine')->getManagerForClass(Reminder::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'oro_reminder_get_requested_reminders_data',
                [$this, 'getRequestedRemindersData']
            )
        ];
    }

    /**
     * Get requested reminders
     *
     * @return array
     */
    public function getRequestedRemindersData()
    {
        /** @var User|null */
        $user = null;
        $token = $this->getSecurityTokenStorage()->getToken();
        if (null !== $token) {
            $user = $token->getUser();
        }

        if ($user instanceof User) {
            $reminders = $this->getEntityManager()
                ->getRepository(Reminder::class)
                ->findRequestedReminders($user);

            return $this->getMessageParamsProvider()->getMessageParamsForReminders($reminders);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oro_reminder.subscriber';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return [
            'security.token_storage' => TokenStorageInterface::class,
            'oro_reminder.web_socket.message_params_provider' => MessageParamsProvider::class,
            'doctrine' => ManagerRegistry::class,
        ];
    }
}
