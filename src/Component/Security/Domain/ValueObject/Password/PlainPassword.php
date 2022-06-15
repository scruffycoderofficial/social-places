<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\ValueObject\Password
{
    use Stringable;

    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;

    /**
     * Class PlainPassword
     *
     * @package BeyondCapable\Component\Security\Domain\ValueObject\Password
     */
    final class PlainPassword implements Stringable
    {
        private function __construct(public string $plainPassword)
        {
        }

        public function __toString(): string
        {
            return $this->plainPassword;
        }

        public static function createFromString(string $plainPassword): PlainPassword
        {
            return new PlainPassword($plainPassword);
        }

        public function hash(PasswordHasherInterface $passwordHasher): HashedPassword
        {
            return $passwordHasher->hashPassword($this);
        }
    }
}
