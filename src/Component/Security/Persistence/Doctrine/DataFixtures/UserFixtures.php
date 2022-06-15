<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Persistence\Doctrine\DataFixtures
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Platform\Domain\ValueObject\Date\DateTime;
    use BeyondCapable\Platform\Domain\ValueObject\Email\EmailAddress;
    use BeyondCapable\Platform\Domain\Exception\InvalidArgumentException;
    use BeyondCapable\Platform\Domain\ValueObject\Identifier\UuidIdentifier;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;
    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;

    use Doctrine\Persistence\ObjectManager;
    use Doctrine\Bundle\FixturesBundle\Fixture;

    /**
     * Class UserFixtures
     *
     * @package BeyondCapable\Component\Security\Persistence\Doctrine\DataFixtures
     */
    final class UserFixtures extends Fixture
    {
        public function __construct(private PasswordHasherInterface $passwordHasher)
        {
        }

        public function load(ObjectManager $manager): void
        {
            try {

                $manager->persist($this->createUser(1));
                $manager->persist($this->createUser(2, expired: true));
                $manager->persist($this->createUser(3, suspended: true));

            } catch (InvalidArgumentException $e) {
            }

            $manager->flush();
        }

        /**
         * @param int $index
         * @param bool $expired
         * @param bool $suspended
         * @return User
         * @throws InvalidArgumentException
         */
        public function createUser(int $index, bool $expired = false, bool $suspended = false): User
        {
            $user = User::create(
                identifier: UuidIdentifier::create(),
                email: EmailAddress::createFromString(sprintf('user+%d@email.com', $index)),
                plainPassword: PlainPassword::createFromString('password'),
                expiredAt: $expired ? DateTime::createFromString('2021-01-01 00:00:00') : null,
                suspendedAt: $suspended ? DateTime::createFromString('2021-01-01 00:00:00') : null
            );

            /** @var PlainPassword $plainPassword */
            $plainPassword = $user->plainPassword;

            $user->hashedPassword = $plainPassword->hash($this->passwordHasher);

            return $user;
        }
    }
}
