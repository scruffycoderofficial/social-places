<?php

declare(strict_types=1);

namespace BeyondCapable\Domain\Tests\Unit\ValueObject\Identifier
{
    use PHPUnit\Framework\TestCase;
    use Symfony\Component\Uid\Uuid;
    use BeyondCapable\Domain\Exception\InvalidArgumentException;
    use BeyondCapable\Domain\ValueObject\Identifier\UuidIdentifier;

    /**
     * Class UuidIdentifierTest
     *
     * @package BeyondCapable\Domain\Tests\Unit\ValueObject\Identifier
     */
    final class UuidIdentifierTest extends TestCase
    {
        /**Test if factories create identifier
         */
        public function testIfFactoriesCreateIdentifier(): void
        {
            $identifier = UuidIdentifier::create();
            $this->assertTrue(Uuid::isValid((string) $identifier));
            $this->assertInstanceOf(Uuid::class, $identifier->uuid());

            $identifier = UuidIdentifier::createFromString((string) Uuid::v4());
            $this->assertTrue(Uuid::isValid((string) $identifier));
            $this->assertInstanceOf(Uuid::class, $identifier->uuid());

            $identifier = UuidIdentifier::createFromUuid(Uuid::v4());
            $this->assertTrue(Uuid::isValid((string) $identifier));
            $this->assertInstanceOf(Uuid::class, $identifier->uuid());
        }

        /**
         * Test if uuid is valid
         */
        public function testIfUuidIsInvalid(): void
        {
            $this->expectException(InvalidArgumentException::class);
            UuidIdentifier::createFromString('fail');
        }
    }
}
