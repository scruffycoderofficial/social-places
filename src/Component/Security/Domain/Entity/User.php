<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Entity
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\DateTime;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\Interval;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Token\UuidToken;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Email\EmailAddress;
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Identifier\UuidIdentifier;

    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\HashedPassword;
    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;

    /**
     * Class User
     *
     * @package BeyondCapable\Component\Security\Domain\Entity
     */
    class User
    {
        private function __construct(
            public UuidIdentifier $identifier,
            public EmailAddress $email,
            public ?HashedPassword $hashedPassword = null,
            public ?PlainPassword $plainPassword = null,
            public ?DateTime $expiredAt = null,
            public ?DateTime $suspendedAt = null,
            public ?UuidToken $forgottenPasswordToken = null,
            public ?DateTime $forgottenPasswordRequestedAt = null
        ) {
        }

        public static function create(
            UuidIdentifier $identifier,
            EmailAddress $email,
            ?HashedPassword $hashedPassword = null,
            ?PlainPassword $plainPassword = null,
            ?DateTime $expiredAt = null,
            ?DateTime $suspendedAt = null,
            ?UuidToken $forgottenPasswordToken = null,
            ?DateTime $forgottenPasswordRequestedAt = null
        ): User {
            return new User(
                $identifier,
                $email,
                $hashedPassword,
                $plainPassword,
                $expiredAt,
                $suspendedAt,
                $forgottenPasswordToken,
                $forgottenPasswordRequestedAt
            );
        }

        public function isExpired(): bool
        {
            return !(null === $this->expiredAt) && $this->expiredAt->isEarlierThan(DateTime::now());
        }

        public function isSupended(): bool
        {
            return !(null === $this->suspendedAt) && $this->suspendedAt->isEarlierThan(DateTime::now());
        }

        public function canResetPassword(): bool
        {
            return !(null === $this->forgottenPasswordRequestedAt)
                && $this->forgottenPasswordRequestedAt
                    ->add(Interval::createFromString('P1D'))
                    ->isLaterThan(DateTime::now());
        }

        public function requestForAForgottenPassword(): void
        {
            $this->forgottenPasswordRequestedAt = DateTime::now();
            $this->forgottenPasswordToken = UuidToken::create();
        }

        public function resetPassword(PasswordHasherInterface $passwordHasher): void
        {
            if (null === $this->plainPassword) {
                throw new InvalidArgumentException('Plain password missing.');
            }

            $this->hashedPassword = $this->plainPassword->hash($passwordHasher);
            $this->plainPassword = null;
            $this->forgottenPasswordRequestedAt = null;
            $this->forgottenPasswordToken = null;
        }

        public function getUserName()
        {
            return $this->username;
        }
    }
}
