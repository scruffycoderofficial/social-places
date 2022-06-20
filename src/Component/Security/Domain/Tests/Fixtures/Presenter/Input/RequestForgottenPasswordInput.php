<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Fixtures\Presenter\Input
{
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordInputInterface;

    use BeyondCapable\Core\Platform\Domain\ValueObject\Email\EmailAddress;

    /**
     * Class RequestForgottenPasswordInput
     *
     * @package BeyondCapable\Component\Security\Domain\Tests\Fixtures\Presenter\Input
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
