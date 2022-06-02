<?php

declare(strict_types=1);

namespace BeyondCapable\Domain\Tests\Unit\ValueObject\Date
{
    use DateInterval;
    use PHPUnit\Framework\TestCase;
    use BeyondCapable\Domain\ValueObject\Date\Interval;
    use BeyondCapable\Domain\Exception\InvalidArgumentException;

    /**
     * Class IntervalTest
     * s
     * @package BeyondCapable\Domain\Tests\Unit\ValueObject\Date
     */
    final class IntervalTest extends TestCase
    {
        public function testIfFactoryCreateInterval(): void
        {
            $interval = Interval::createFromString('P1D');
            $this->assertInstanceOf(DateInterval::class, $interval->toDateInterval());
            $this->assertEquals('P1D', (string) $interval);
        }

        public function testIfIntervalIsInvalid(): void
        {
            $this->expectException(InvalidArgumentException::class);
            Interval::createFromString('FAIL');
        }
    }
}
