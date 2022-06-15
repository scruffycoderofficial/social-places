<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Input
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;
    use BeyondCapable\Component\Security\Domain\UseCase\ResetPassword\ResetPasswordInputInterface;

    use Symfony\Component\Validator\Constraints\NotBlank;

    /**
     * Class ResetPasswordInput
     *
     * @package BeyondCapable\Component\Security\Presenter\Input
     */
    final class ResetPasswordInput implements ResetPasswordInputInterface
    {
        #[NotBlank]
        public string $password;

        public function __construct(private User $user)
        {
        }

        public function plainPassword(): PlainPassword
        {
            return PlainPassword::createFromString($this->password);
        }

        public function user(): User
        {
            return $this->user;
        }
    }
}
