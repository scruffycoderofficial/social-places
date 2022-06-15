<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Core\EventListener
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Core\User\UserProxy;
    use BeyondCapable\Component\Security\Core\Authenticator\Passport\PasswordCredentials;
    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;

    use Symfony\Component\Security\Http\Event\CheckPassportEvent;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
    use Symfony\Component\Security\Core\Exception\BadCredentialsException;

    /**
     * Class CheckCredentialsListener
     *
     * @package BeyondCapable\Component\Security\Core\EventListener
     */
    final class CheckCredentialsListener implements EventSubscriberInterface
    {
        public function __construct(private PasswordHasherInterface $passwordHasher)
        {
        }

        public function checkPassport(CheckPassportEvent $checkPassportEvent): void
        {
            /** @var Passport $passport */
            $passport = $checkPassportEvent->getPassport();
            $userProxy = $passport->getUser();
            if (!$userProxy instanceof UserProxy || !$passport->hasBadge(PasswordCredentials::class)) {
                return;
            }
            /** @var PasswordCredentials $passwordCredentials */
            $passwordCredentials = $passport->getBadge(PasswordCredentials::class);
            if ($passwordCredentials->isResolved()) {
                return;
            }
            /** @var User $user */
            $user = $userProxy->user;
            if (null === $user->hashedPassword) {
                throw new BadCredentialsException('Invalid credentials.');
            }
            $passwordCredentials->verify($this->passwordHasher, $user->hashedPassword);
        }

        /**
         * @return array<string, string>
         */
        public static function getSubscribedEvents(): array
        {
            return [CheckPassportEvent::class => 'checkPassport'];
        }
    }
}
