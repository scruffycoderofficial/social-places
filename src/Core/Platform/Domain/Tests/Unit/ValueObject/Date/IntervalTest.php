<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\Tests\Unit\ValueObject\Date
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\Interval;
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use DateInterval;

    use PHPUnit\Framework\TestCase;

    /**
     * Class IntervalTest
     *
     * @package BeyondCapable\Core\Platform\Domain\Tests\Unit\ValueObject\Date
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
