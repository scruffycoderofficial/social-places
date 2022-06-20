<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\Tests\Unit\ValueObject\Date
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\Date;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\Time;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\DateTime;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\Interval;
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use Generator;
    use DateTimeImmutable;
    use DateTimeInterface;

    use PHPUnit\Framework\TestCase;

    /**
     * Class DateTimeTest
     *
     * @package BeyondCapable\Core\Platform\Domain\Tests\Unit\ValueObject\Date
     */
    final class DateTimeTest extends TestCase
    {
        public function testIfFactoriesCreateDateTimes(): void
        {
            $this->assertDateTime(DateTime::create(2021, 12, 15, 12, 30, 45));
            $this->assertDateTime(DateTime::createFromDateAndTime(
                Date::create(2021, 12, 15),
                Time::create(12, 30, 45)
            ));
            $this->assertDateTime(DateTime::createFromDateTime(new DateTimeImmutable('2021-12-15 12:30:45')));
            $this->assertDateTime(DateTime::createFromString('2021-12-15 12:30:45'));
        }

        /**
         * @dataProvider provideInvalidDateTime
         */
        public function testIfDateTimeIsInvalid(
            int $year,
            int $month,
            int $day,
            int $hours,
            int $minutes,
            int $seconds
        ): void {
            $this->expectException(InvalidArgumentException::class);
            DateTime::create($year, $month, $day, $hours, $minutes, $seconds);
        }

        /**
         * @return Generator<string, array<array-key, int>>
         */
        public function provideInvalidDateTime(): Generator
        {
            yield 'invalid month' => [2021, 13, 1, 12, 30, 45];
            yield 'invalid day' => [2021, 12, 32, 12, 30, 45];
            yield 'invalid hours' => [2021, 12, 1, 24, 30, 45];
            yield 'invalid minutes' => [2021, 12, 1, 12, 60, 45];
            yield 'invalid seconds' => [2021, 12, 1, 12, 30, 60];
        }

        private function assertDateTime(DateTime $dateTime): void
        {
            $this->assertEquals(2021, $dateTime->date()->year());
            $this->assertEquals(12, $dateTime->date()->month());
            $this->assertEquals(15, $dateTime->date()->day());
            $this->assertEquals(12, $dateTime->time()->hours());
            $this->assertEquals(30, $dateTime->time()->minutes());
            $this->assertEquals(45, $dateTime->time()->seconds());
            $this->assertEquals('2021-12-15 12:30:45', (string) $dateTime);
            $this->assertInstanceOf(DateTimeInterface::class, $dateTime->toDateTime());
            $this->assertTrue($dateTime->isLaterThan(DateTime::createFromString('2020-01-01')));
            $this->assertTrue($dateTime->isEarlierThan(DateTime::createFromString('2022-01-01')));
            $newDateTime = $dateTime->add(Interval::createFromString('P1D'));
            $this->assertEquals('2021-12-16 12:30:45', (string) $newDateTime);
            $newDateTime = $dateTime->sub(Interval::createFromString('P1D'));
            $this->assertEquals('2021-12-14 12:30:45', (string) $newDateTime);
        }
    }
}
