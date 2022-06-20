<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Unit\Domain\ValueObject\Password
{
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\HashedPassword;

    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;

    use PHPUnit\Framework\TestCase;

    /**
     * Class HashedPasswordTest
     *
     * @package BeyondCapable\Component\Security\Domain\Tests\Unit\Domain\ValueObject\Password
     */
    final class HashedPasswordTest extends TestCase
    {
        public function testIfFactoryCreateHashedPassword(): void
        {
            $hashedPassword = HashedPassword::createFromString('test');
            $this->assertEquals('test', (string) $hashedPassword);

            $plainPassword = PlainPassword::createFromString('test');

            $passwordHasher = $this->createMock(PasswordHasherInterface::class);
            $passwordHasher->method('verifyPassword')->willReturn(true);

            $this->assertTrue($hashedPassword->verify($passwordHasher, $plainPassword));
        }
    }
}
