<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword
{
    /**
     * Interface RequestForgottenPasswordPresenterInterface
     *
     * @package BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword
     */
    interface RequestForgottenPasswordPresenterInterface
    {
        public function present(RequestForgottenPasswordOutput $output): void;
    }
}
