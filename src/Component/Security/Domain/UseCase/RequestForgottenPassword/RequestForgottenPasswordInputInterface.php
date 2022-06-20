<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Email\EmailAddress;

    /**
     * Interface RequestForgottenPasswordInputInterface
     *
     * @package BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword
     */
    interface RequestForgottenPasswordInputInterface
    {
        public function email(): EmailAddress;
    }
}
