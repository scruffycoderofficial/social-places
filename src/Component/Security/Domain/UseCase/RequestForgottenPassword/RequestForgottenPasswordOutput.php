<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword
{
    use BeyondCapable\Component\Security\Domain\Entity\User;

    /**
     * Class RequestForgottenPasswordOutput
     *
     * @package BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword
     */
    final class RequestForgottenPasswordOutput
    {
        public function __construct(public User $user)
        {
        }
    }
}
