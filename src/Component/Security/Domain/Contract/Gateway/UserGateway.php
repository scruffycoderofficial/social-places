<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Contract\Gateway
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Platform\Domain\ValueObject\Token\UuidToken;
    use BeyondCapable\Platform\Domain\ValueObject\Email\EmailAddress;

    /**
     * @template T as User
     */
    interface UserGateway
    {
        public function getUserByEmail(EmailAddress $email): ?User;

        public function getUserByForgottenPasswordToken(UuidToken $token): ?User;

        public function update(User $user): void;
    }
}
