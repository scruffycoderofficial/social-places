<?php

declare(strict_types=1);

namespace BeyondCapable\Platform\Persistence\Doctrine\Type
{
    use DateTimeInterface;
    use Doctrine\DBAL\Platforms\AbstractPlatform;
    use BeyondCapable\Platform\Domain\ValueObject\Date\Date;
    use Doctrine\DBAL\Types\DateType as DoctrineDateType;

    /**
     * Class DateType
     *
     * @package BeyondCapable\Platform\Persistence\Doctrine\Type
     */
    final class DateType extends DoctrineDateType
    {
        public const NAME = 'date';

        public function getName(): string
        {
            return self::NAME;
        }

        /**
         * @param ?Date $value
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
        public function convertToPHPValue($value, AbstractPlatform $platform): ?Date
        {
            if (null === $value) {
                return null;
            }

            /** @var DateTimeInterface $dateTime */
            $dateTime = parent::convertToPHPValue($value, $platform);

            return Date::createFromDateTime($dateTime);
        }
    }
}
