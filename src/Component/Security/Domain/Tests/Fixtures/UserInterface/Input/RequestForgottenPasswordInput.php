<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Fixtures\UserInterface\Input
{
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordInputInterface;
    use BeyondCapable\Platform\Domain\ValueObject\Email\EmailAddress;

    /**
     * Class RequestForgottenPasswordInput
     *
     * @package BeyondCapable\Component\Security\Domain\Tests\Fixtures\UserInterface\Input
     */
    final class RequestForgottenPasswordInput implements RequestForgottenPasswordInputInterface
    {
        public function __construct(private string $email)
        {
        }

        public function email(): EmailAddress
        {
            return EmailAddress::createFromString($this->email);
        }
    }
}
