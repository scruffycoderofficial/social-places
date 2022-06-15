<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Contract\PasswordHasher
{
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\HashedPassword;

    /**
     * Interface PasswordHasherInterface
     *
     * @package BeyondCapable\Component\Security\Domain\Contract\PasswordHasher
     */
    interface PasswordHasherInterface
    {
        public function hashPassword(PlainPassword $plainPassword): HashedPassword;

        public function verifyPassword(HashedPassword $hashedPassword, PlainPassword $plainPassword): bool;
    }
}
