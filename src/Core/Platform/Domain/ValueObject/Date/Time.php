<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\ValueObject\Date
{
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use Exception;
    use DateTimeImmutable;

    /**
     * Class Time
     *
     * @package BeyondCapable\Core\Platform\Domain\ValueObject\Date
     */
    final class Time extends AbstractDateTime implements DateTimeInterface
    {
        /**
         * Time constructor.
         *
         * @param int $hours
         * @param int $minutes
         * @param int $seconds
         * @throws InvalidArgumentException
         */
        private function __construct(private int $hours, private int $minutes, private int $seconds)
        {
            if ($this->hours < 0 || $this->hours > 23) {
                throw new InvalidArgumentException('Hours should be between 0 and 23');
            }

            if ($this->minutes < 0 || $this->minutes > 59) {
                throw new InvalidArgumentException('Minutes should be between 0 and 59');
            }

            if ($this->seconds < 0 || $this->seconds > 59) {
                throw new InvalidArgumentException('Seconds should be between 0 and 59');
            }
        }

        /**
         * @return string
         */
        public function __toString(): string
        {
            return sprintf('%02d:%02d:%02d', $this->hours, $this->minutes, $this->seconds);
        }

        /**
         * @param DateTimeInterface $dateTime
         * @return Time
         * @throws InvalidArgumentException
         */
        public static function createFromDateTime(\DateTimeInterface $dateTime): Time
        {
            return self::create(
                intval($dateTime->format('H')),
                intval($dateTime->format('i')),
                intval($dateTime->format('s'))
            );
        }

        /**
         * @param string $time
         * @return Time
         * @throws Exception
         */
        public static function createFromString(string $time): Time
        {
            try {

                return self::createFromDateTime(new DateTimeImmutable($time));

            } catch (InvalidArgumentException $e) {
            }
        }

        /**
         * @param int $hours
         * @param int $minutes
         * @param int $seconds
         * @return Time
         * @throws InvalidArgumentException
         */
        public static function create(int $hours, int $minutes, int $seconds): Time
        {
            return new Time($hours, $minutes, $seconds);
        }

        /**
         * @param Interval $interval
         * @return Time
         * @throws \Exception
         */
        public function add(Interval $interval): Time
        {
            /** @var DateTimeImmutable $dateTime */
            $dateTime = $this->toDateTime();

            return self::createFromDateTime($dateTime->add($interval->toDateInterval()));
        }

        /**
         * @param Interval $interval
         * @return Time
         * @throws \Exception
         */
        public function sub(Interval $interval): Time
        {
            /** @var DateTimeImmutable $dateTime */
            $dateTime = $this->toDateTime();

            return self::createFromDateTime($dateTime->sub($interval->toDateInterval()));
        }

        /**
         * @codeCoverageIgnore
         */
        public static function now(): Time
        {
            return self::createFromDateTime(new DateTimeImmutable());
        }

        /**
         * @return DateTimeInterface
         * @throws Exception
         */
        public function toDateTime(): \DateTimeInterface
        {
            return new DateTimeImmutable((string) $this);
        }

        public function hours(): int
        {
            return $this->hours;
        }

        public function minutes(): int
        {
            return $this->minutes;
        }

        public function seconds(): int
        {
            return $this->seconds;
        }
    }
}
