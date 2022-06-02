<?php

declare(strict_types=1);

namespace BeyondCapable\Domain\Tests\Unit\ValueObject\Date
{
    use Generator;
    use DateTimeImmutable;
    use DateTimeInterface;
    use PHPUnit\Framework\TestCase;
    use BeyondCapable\Domain\ValueObject\Date\Time;
    use BeyondCapable\Domain\ValueObject\Date\DateTime;
    use BeyondCapable\Domain\ValueObject\Date\Interval;
    use BeyondCapable\Domain\Exception\InvalidArgumentException;

    /**
     * Class TimeTest
     *
     * @package BeyondCapable\Domain\Tests\Unit\ValueObject\Date
     */
    final class TimeTest extends TestCase
    {
        public function testIfFactoriesCreateTimes(): void
        {
            $this->assertTime(Time::create(12, 30, 45));
            $this->assertTime(Time::createFromDateTime(new DateTimeImmutable('12:30:45')));
            $this->assertTime(Time::createFromString('12:30:45'));
        }

        /**
         * @dataProvider provideInvalidTime
         */
        public function testIfTimeIsInvalid(int $hours, int $minutes, int $seconds): void
        {
            $this->expectException(InvalidArgumentException::class);
            Time::create($hours, $minutes, $seconds);
        }

        /**
         * @return Generator<string, array<array-key, int>>
         */
        public function provideInvalidTime(): Generator
        {
            yield 'invalid hours' => [24, 0, 0];
            yield 'invalid minutes' => [12, 60, 0];
            yield 'invalid seconds' => [12, 0, 60];
        }

        private function assertTime(Time $time): void
        {
            $this->assertEquals(12, $time->hours());
            $this->assertEquals(30, $time->minutes());
            $this->assertEquals(45, $time->seconds());
            $this->assertEquals('12:30:45', (string) $time);
            $this->assertInstanceOf(DateTimeInterface::class, $time->toDateTime());
            $this->assertTrue($time->isLaterThan(DateTime::createFromString('10:00:00')));
            $this->assertTrue($time->isEarlierThan(DateTime::createFromString('14:00:00')));
            $newTime = $time->add(Interval::createFromString('PT1H'));
            $this->assertEquals('13:30:45', (string) $newTime);
            $newTime = $time->sub(Interval::createFromString('PT1H'));
            $this->assertEquals('11:30:45', (string) $newTime);
        }
    }
}
