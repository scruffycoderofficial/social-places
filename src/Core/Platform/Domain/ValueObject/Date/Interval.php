<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\ValueObject\Date
{
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use Exception;
    use DateInterval;

    /**
     * Class Interval
     *
     * @package BeyondCapable\Core\Platform\Domain\ValueObject\Date
     */
    final class Interval
    {
        /**
         * Interval constructor.
         *
         * @param string $interval
         */
        private function __construct(public string $interval)
        {
        }

        /**
         * @return string
         */
        public function __toString(): string
        {
            return $this->interval;
        }

        /**
         * @param string $interval
         * @return Interval
         * @throws InvalidArgumentException
         */
        public static function createFromString(string $interval): Interval
        {
            try {
                new DateInterval($interval);
            } catch (Exception) {
                throw new InvalidArgumentException('Interval invalid.');
            }

            return new Interval($interval);
        }

        /**
         * @return DateInterval
         * @throws Exception
         */
        public function toDateInterval(): DateInterval
        {
            return new DateInterval($this->interval);
        }
    }
}
