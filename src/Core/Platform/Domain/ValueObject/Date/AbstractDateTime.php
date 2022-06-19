<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\ValueObject\Date
{
    /**
     * Class AbstractDateTime
     *
     * @package BeyondCapable\Core\Platform\Domain\ValueObject\Date
     */
    abstract class AbstractDateTime implements DateTimeInterface
    {
        /**
         * @param DateTimeInterface $date
         * @return bool
         */
        public function isEarlierThan(DateTimeInterface $date): bool
        {
            return $this->toDateTime() < $date->toDateTime();
        }

        /**
         * @param DateTimeInterface $date
         * @return bool
         */
        public function isLaterThan(DateTimeInterface $date): bool
        {
            return $this->toDateTime() > $date->toDateTime();
        }
    }
}
