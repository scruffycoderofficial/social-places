<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\UseCase\ResetPassword
{
    use Exception;

    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Domain\Contract\Gateway\UserGateway;
    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;

    /**
     * Class ResetPassword
     *
     * @package BeyondCapable\Component\Security\Domain\UseCase\ResetPassword
     */
    final class ResetPassword implements ResetPasswordInterface
    {
        /**
         * @param UserGateway<User> $userGateway
         */
        public function __construct(private UserGateway $userGateway, private PasswordHasherInterface $passwordHasher)
        {
        }

        public function __invoke(ResetPasswordInputInterface $input): void
        {
            $user = $input->user();

            if (!$user->canResetPassword()) {
                throw new Exception('You need to make a forgotten password request.');
            }

            $user->plainPassword = $input->plainPassword();
            $user->resetPassword($this->passwordHasher);
            $this->userGateway->update($user);
        }
    }
}
