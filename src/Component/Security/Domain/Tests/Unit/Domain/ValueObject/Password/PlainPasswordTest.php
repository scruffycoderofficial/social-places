<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Unit\ValueObject\Password
{
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\HashedPassword;
    use BeyondCapable\Component\Security\Domain\Contract\PasswordHasher\PasswordHasherInterface;

    use PHPUnit\Framework\TestCase;

    /**
     * Class PlainPasswordTest
     *
     * @package BeyondCapable\Component\Security\Domain\Tests\Unit\ValueObject\Password
     */
    final class PlainPasswordTest extends TestCase
    {
        public function testIfFactoryCreateHashedPassword(): void
        {
            $plainPassword = PlainPassword::createFromString('test');
            $this->assertEquals('test', (string) $plainPassword);

            $passwordHasher = $this->createMock(PasswordHasherInterface::class);
            $passwordHasher->method('verifyPassword')->willReturn(true);

            $this->assertInstanceOf(HashedPassword::class, $plainPassword->hash($passwordHasher));
        }
    }
}
