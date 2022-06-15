<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\UseCase\ResetPassword
{
    /**
     * Interface ResetPasswordInterface
     *
     * @package BeyondCapable\Component\Security\Domain\UseCase\ResetPassword
     */
    interface ResetPasswordInterface
    {
        public function __invoke(ResetPasswordInputInterface $input): void;
    }
}
