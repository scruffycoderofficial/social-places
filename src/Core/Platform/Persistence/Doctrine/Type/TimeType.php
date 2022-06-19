<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Persistence\Doctrine\Type
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\Time;

    use DateTimeInterface;
    use Doctrine\DBAL\Platforms\AbstractPlatform;
    use Doctrine\DBAL\Types\TimeType as DoctrineTimeType;

    /**
     * Class TimeType
     *
     * @package BeyondCapable\Core\Platform\Persistence\Doctrine\Type
     */
    final class TimeType extends DoctrineTimeType
    {
        public const NAME = 'time';

        public function getName(): string
        {
            return self::NAME;
        }

        /**
         * @param ?Time $value
         */
        public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
        {
            if (null === $value) {
                return null;
            }

            /* @phpstan-ignore-next-line */
            return parent::convertToDatabaseValue($value->toDateTime(), $platform);
        }

        /**
         * @param ?string $value
         */
        public function convertToPHPValue($value, AbstractPlatform $platform): ?Time
        {
            if (null === $value) {
                return null;
            }

            /** @var DateTimeInterface $dateTime */
            $dateTime = parent::convertToPHPValue($value, $platform);

            return Time::createFromDateTime($dateTime);
        }
    }
}
