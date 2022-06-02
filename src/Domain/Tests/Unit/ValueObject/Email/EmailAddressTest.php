<?php

declare(strict_types=1);

namespace BeyondCapable\Domain\Tests\Unit\ValueObject\Email
{
    use PHPUnit\Framework\TestCase;
    use BeyondCapable\Domain\ValueObject\Email\EmailAddress;
    use BeyondCapable\Domain\Exception\InvalidArgumentException;

    /**
     * Class EmailAddressTest
     *
     * @package BeyondCapable\Domain\Tests\Unit\ValueObject\Email
     */
    final class EmailAddressTest extends TestCase
    {
        /**
         * Test if factories create email address
         */
        public function testIfFactoryCreateEmailAddress(): void
        {
            $emailAddress = EmailAddress::createFromString('user+1@email.com');
            $this->assertEquals('user+1@email.com', (string) $emailAddress);
            $this->assertTrue($emailAddress->equalTo(EmailAddress::createFromString('user+1@email.com')));
        }

        /**
         * Test if email address is valid
         */
        public function testIfEmailAddressIsInvalid(): void
        {
            $this->expectException(InvalidArgumentException::class);
            EmailAddress::createFromString('fail');
        }
    }
}
