<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Core\User
{
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\User\UserCheckerInterface;
    use Symfony\Component\Security\Core\Exception\AccountExpiredException;
    use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

    /**
     * Class Checker
     *
     * @package BeyondCapable\Component\Security\Core\User
     */
    final class Checker implements UserCheckerInterface
    {
        public function checkPreAuth(UserInterface $user): void
        {
            if (!$user instanceof UserProxy) {
                return;
            }

            if ($user->user->isSupended()) {
                throw new CustomUserMessageAccountStatusException('Your user account has been suspended.');
            }
        }

        public function checkPostAuth(UserInterface $user): void
        {
            if (!$user instanceof UserProxy) {
                return;
            }

            // user account is expired, the user may be notified
            if ($user->user->isExpired()) {
                throw new AccountExpiredException();
            }
        }
    }
}
