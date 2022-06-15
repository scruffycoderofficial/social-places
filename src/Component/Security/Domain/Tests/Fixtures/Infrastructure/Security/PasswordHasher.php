<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Fixtures\Infrastructure\Security
{
    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\HashedPassword;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;

    /**
     * Class PasswordHasher
     *
     * @package BeyondCapable\Component\Security\Domain\Tests\Fixtures\Infrastructure\Security
     */
    final class PasswordHasher implements PasswordHasherInterface
    {
        public function hashPassword(PlainPassword $plainPassword): HashedPassword
        {
            return HashedPassword::createFromString(password_hash((string) $plainPassword, PASSWORD_BCRYPT));
        }

        public function verifyPassword(HashedPassword $hashedPassword, PlainPassword $plainPassword): bool
        {
            return password_verify((string) $plainPassword, (string) $hashedPassword);
        }
    }
}
