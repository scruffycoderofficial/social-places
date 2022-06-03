<?php

declare(strict_types=1);

namespace BeyondCapable\Platform\Domain\ValueObject\Date
{
    use Exception;
    use DateTimeImmutable;
    use BeyondCapable\Platform\Domain\Exception\InvalidArgumentException;

    /**
     * Class Date
     *
     * @package BeyondCapable\Platform\Domain\ValueObject\Date
     */
    final class Date extends AbstractDateTime implements DateTimeInterface
    {
        /**
         * Date constructor.
         *
         * @param int $year
         * @param int $month
         * @param int $day
         * @throws InvalidArgumentException
         */
        private function __construct(private int $year, private int $month, private int $day)
        {
            if ($this->month < 1 || $this->month > 12) {
                throw new InvalidArgumentException('Month should be between 1 and 12');
            }

            if ($this->day < 1 || $this->day > 31) {
                throw new InvalidArgumentException('Day should be between 1 and 31');
            }
        }

        /**
         * @return string
         */
        public function __toString(): string
        {
            return sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
        }

        /**
         * @param int $year
         * @param int $month
         * @param int $day
         * @return Date
         * @throws InvalidArgumentException
         */
        public static function create(int $year, int $month, int $day): Date
        {
            return new Date($year, $month, $day);
        }

        /**
         * @param string $dateTime
         * @return Date
         * @throws Exception
         */
        public static function createFromString(string $dateTime): Date
        {
            return self::createFromDateTime(new DateTimeImmutable($dateTime));
        }

        /**
         * @param \DateTimeInterface $dateTime
         * @return Date
         * @throws InvalidArgumentException
         */
        public static function createFromDateTime(\DateTimeInterface $dateTime): Date
        {
            return self::create(
                intval($dateTime->format('Y')),
                intval($dateTime->format('m')),
                intval($dateTime->format('d'))
            );
        }

        /**
         * @codeCoverageIgnore
         * @throws InvalidArgumentException
         */
        public static function now(): Date
        {
            return self::createFromDateTime(new DateTimeImmutable());
        }

        /**
         * @param Interval $interval
         * @return Date
         * @throws InvalidArgumentException
         * @throws Exception
         */
        public function add(Interval $interval): Date
        {
            /** @var DateTimeImmutable $dateTime */
            $dateTime = $this->toDateTime();

            return self::createFromDateTime($dateTime->add($interval->toDateInterval()));
        }

        /**
         * @param Interval $interval
         * @return Date
         * @throws InvalidArgumentException
         * @throws Exception
         */
        public function sub(Interval $interval): Date
        {
            /** @var DateTimeImmutable $dateTime */
            $dateTime = $this->toDateTime();

            return self::createFromDateTime($dateTime->sub($interval->toDateInterval()));
        }

        /**
         * @return \DateTimeInterface
         * @throws Exception
         */
        public function toDateTime(): \DateTimeInterface
        {
            return new DateTimeImmutable((string) $this);
        }

        /**
         * @return int
         */
        public function year(): int
        {
            return $this->year;
        }

        /**
         * @return int
         */
        public function month(): int
        {
            return $this->month;
        }

        /**
         * @return int
         */
        public function day(): int
        {
            return $this->day;
        }
    }
}
