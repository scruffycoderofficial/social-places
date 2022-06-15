<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\UseCase\ResetPassword
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;

    /**
     * Interface ResetPasswordInputInterface
     *
     * @package BeyondCapable\Component\Security\Domain\UseCase\ResetPassword
     */
    interface ResetPasswordInputInterface
    {
        public function plainPassword(): PlainPassword;

        public function user(): User;
    }
}
