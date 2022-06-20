<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Core\User
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Domain\Contract\Gateway\UserGateway;

    use BeyondCapable\Core\Platform\Domain\ValueObject\EmailAddress;
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\User\UserProviderInterface;
    use Symfony\Component\Security\Core\Exception\UserNotFoundException;

    /**
     * Class Provider
     *
     * @package BeyondCapable\Component\Security\Core\User
     */
    final class Provider implements UserProviderInterface
    {
        /**
         * @param UserGateway<User> $userGateway
         */
        public function __construct(private UserGateway $userGateway)
        {
        }

        public function refreshUser(UserInterface $user): UserInterface
        {
            return $this->loadUserByIdentifier($user->getUserIdentifier());
        }

        public function supportsClass(string $class): bool
        {
            return UserProxy::class === $class;
        }

        public function loadUserByUsername(string $username): UserInterface
        {
            return $this->loadUserByIdentifier($username);
        }

        public function loadUserByIdentifier(string $identifier): UserInterface
        {
            try {
                $email = EmailAddress::createFromString($identifier);
            } catch (InvalidArgumentException) {
                throw new UserNotFoundException();
            }

            $user = $this->userGateway->getUserByEmail($email);

            if (null === $user) {
                throw new UserNotFoundException();
            }

            return new UserProxy($user);
        }
    }
}
