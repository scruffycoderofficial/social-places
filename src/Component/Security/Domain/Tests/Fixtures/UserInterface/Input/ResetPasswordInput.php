<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Fixtures\UserInterface\Input
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Domain\UseCase\ResetPassword\ResetPasswordInputInterface;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;

    /**
     * Class ResetPasswordInput
     *
     * @package BeyondCapable\Component\Security\Domain\Tests\Fixtures\UserInterface\Input
     */
    final class ResetPasswordInput implements ResetPasswordInputInterface
    {
        public function __construct(private string $password, private User $user)
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
