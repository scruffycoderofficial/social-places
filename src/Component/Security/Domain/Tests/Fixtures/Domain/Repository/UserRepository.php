<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Fixtures\Infrastructure\Repository
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Domain\Contract\Gateway\UserGateway;

    use BeyondCapable\Core\Platform\Domain\ValueObject\Email\EmailAddress;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Token\UuidToken;

    /**
     * @template-implements UserGateway<User>
     */
    final class UserRepository implements UserGateway
    {
        /**
         * @param array<int, User> $users
         */
        public function __construct(public array $users)
        {
        }

        public function getUserByEmail(EmailAddress $email): ?User
        {
            /** @var array<int, User> $users */
            $users = array_filter($this->users, static fn (User $user) => $user->email->equalTo($email));

            return $users[0] ?? null;
        }

        public function getUserByForgottenPasswordToken(UuidToken $token): ?User
        {
            /** @var array<int, User> $users */
            $users = array_filter(
                $this->users,
                static fn (User $user) => null !== $user->forgottenPasswordToken
                    && $user->forgottenPasswordToken->equalTo($token)
            );

            return $users[0] ?? null;
        }

        public function update(User $user): void
        {
        }
    }
}
