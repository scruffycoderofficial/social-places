<?php

declare(strict_types=1);

namespace BeyondCapable\Platform\Persistence\Doctrine\Type
{
    use DateTimeInterface;
    use Doctrine\DBAL\Platforms\AbstractPlatform;
    use BeyondCapable\Platform\Domain\ValueObject\Date\DateTime;
    use Doctrine\DBAL\Types\DateTimeType as DoctrineDateTimeType;

    /**
     * Class DateTimeType
     *
     * @package BeyondCapable\Platform\Persistance\Doctrine\Type
     */
    final class DateTimeType extends DoctrineDateTimeType
    {
        public const NAME = 'date_time';

        public function getName(): string
        {
            return self::NAME;
        }

        /**
         * @param ?DateTime $value
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
        public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTime
        {
            if (null === $value) {
                return null;
            }

            /** @var DateTimeInterface $dateTime */
            $dateTime = parent::convertToPHPValue($value, $platform);

            return DateTime::createFromDateTime($dateTime);
        }
    }
}
