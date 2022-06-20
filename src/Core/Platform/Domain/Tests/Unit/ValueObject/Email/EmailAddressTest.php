<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\Tests\Unit\ValueObject\Email
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Email\EmailAddress;
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use PHPUnit\Framework\TestCase;

    /**
     * Class EmailAddressTest
     *
     * @package BeyondCapable\Core\Platform\Domain\Tests\Unit\ValueObject\Email
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
