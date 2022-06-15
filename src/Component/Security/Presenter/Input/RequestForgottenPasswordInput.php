<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Input
{
    use BeyondCapable\Platform\Domain\Exception\InvalidArgumentException;
    use BeyondCapable\Platform\Domain\ValueObject\Email\EmailAddress;
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordInputInterface;

    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Constraints\NotBlank;

    /**
     * Class RequestForgottenPasswordInput
     *
     * @package BeyondCapable\Component\Security\Presenter\Input
     */
    final class RequestForgottenPasswordInput implements RequestForgottenPasswordInputInterface
    {
        #[NotBlank]
        #[Email]
        public string $email;

        public function email(): EmailAddress
        {
            try {

                return EmailAddress::createFromString($this->email);

            } catch (InvalidArgumentException $e) {
            }
        }
    }
}
