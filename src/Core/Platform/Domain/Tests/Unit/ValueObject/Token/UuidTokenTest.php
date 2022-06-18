<?php

declare(strict_types=1);

namespace BeyondCapable\Platform\Domain\Tests\Unit\ValueObject\Token
{
    use PHPUnit\Framework\TestCase;
    use Symfony\Component\Uid\Uuid;
    use BeyondCapable\Platform\Domain\ValueObject\Token\UuidToken;
    use BeyondCapable\Platform\Domain\Exception\InvalidArgumentException;

    /**
     * Class UuidTokenTest
     *
     * @package BeyondCapable\Domain\Tests\Unit\ValueObject\Token
     */
    final class UuidTokenTest extends TestCase
    {
        /**
         * Test if factories create token
         */
        public function testIfFactoriesCreateToken(): void
        {
            $token = UuidToken::create();
            $this->assertTrue(Uuid::isValid((string) $token));
            $this->assertInstanceOf(Uuid::class, $token->uuid());

            $token = UuidToken::createFromString((string) Uuid::v4());
            $this->assertTrue(Uuid::isValid((string) $token));
            $this->assertInstanceOf(Uuid::class, $token->uuid());

            $uuid = Uuid::v4();

            $token = UuidToken::createFromUuid($uuid);
            $this->assertTrue(Uuid::isValid((string) $token));
            $this->assertInstanceOf(Uuid::class, $token->uuid());
            $this->assertTrue($token->equalTo(UuidToken::createFromUuid($uuid)));
        }

        /**
         * Test if uuid is valid
         */
        public function testIfUuidIsInvalid(): void
        {
            $this->expectException(InvalidArgumentException::class);
            UuidToken::createFromString('fail');
        }
    }
}
