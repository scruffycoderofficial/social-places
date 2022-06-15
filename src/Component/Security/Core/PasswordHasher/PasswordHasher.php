<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Core\PasswordHasher
{
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\HashedPassword;
    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;

    use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

    /**
     * Class PasswordHasher
     *
     * @package BeyondCapable\Component\Security\Core\PasswordHasher
     */
    final class PasswordHasher implements PasswordHasherInterface
    {
        public function __construct(private NativePasswordHasher $decorated)
        {
        }

        public function hashPassword(PlainPassword $plainPassword): HashedPassword
        {
            return HashedPassword::createFromString($this->decorated->hash((string) $plainPassword));
        }

        public function verifyPassword(HashedPassword $hashedPassword, PlainPassword $plainPassword): bool
        {
            return $this->decorated->verify((string) $hashedPassword, (string) $plainPassword);
        }
    }
}
