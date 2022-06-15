<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Core\Authenticator\Passport
{
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\HashedPassword;
    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;

    use Symfony\Component\Security\Core\Exception\BadCredentialsException;
    use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CredentialsInterface;

    /**
     * Class PasswordCredentials
     *
     * @package BeyondCapable\Component\Security\Core\Authenticator\Passport
     */
    final class PasswordCredentials implements CredentialsInterface
    {
        private bool $resolved = false;

        public function __construct(private PlainPassword $plainPassword)
        {
        }

        public function verify(PasswordHasherInterface $passwordHasher, HashedPassword $hashedPassword): void
        {
            if (!$hashedPassword->verify($passwordHasher, $this->plainPassword)) {
                throw new BadCredentialsException('Invalid credentials.');
            }

            $this->resolved = true;
        }

        public function isResolved(): bool
        {
            return $this->resolved;
        }
    }
}
