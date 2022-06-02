<?php

declare(strict_types=1);

namespace BeyondCapable\Domain\ValueObject\Date
{
    use Stringable;

    /**
     * Interface DateTimeInterface
     *
     * @package BeyondCapable\Domain\ValueObject\Date
     */
    interface DateTimeInterface extends Stringable
    {
        /**
         * @param \DateTimeInterface $dateTime
         * @return DateTimeInterface
         */
        public static function createFromDateTime(\DateTimeInterface $dateTime): DateTimeInterface;

        /**
         * @param string $dateTime
         * @return DateTimeInterface
         */
        public static function createFromString(string $dateTime): DateTimeInterface;

        /**
         * @return DateTimeInterface
         */
        public static function now(): DateTimeInterface;

        /**
         * @return \DateTimeInterface
         */
        public function toDateTime(): \DateTimeInterface;

        /**
         * @param DateTimeInterface $date
         * @return bool
         */
        public function isEarlierThan(DateTimeInterface $date): bool;

        /**
         * @param DateTimeInterface $date
         * @return bool
         */
        public function isLaterThan(DateTimeInterface $date): bool;

        /**
         * @param Interval $interval
         * @return DateTimeInterface
         */
        public function add(Interval $interval): DateTimeInterface;

        /**
         * @param Interval $interval
         * @return DateTimeInterface
         */
        public function sub(Interval $interval): DateTimeInterface;
    }
}
