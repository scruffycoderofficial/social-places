<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword
{
    use BeyondCapable\Platform\Domain\Exception\InvalidArgumentException;

    /**
     * Interface RequestForgottenPasswordInterface
     *
     * @package BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword
     */
    interface RequestForgottenPasswordInterface
    {
        /**
         * @throws InvalidArgumentException
         */
        public function __invoke(
            RequestForgottenPasswordInputInterface $input,
            RequestForgottenPasswordPresenterInterface $presenter
        ): void;
    }
}
