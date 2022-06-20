<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Persistence\Doctrine\Type
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Identifier\UuidIdentifier;

    use Doctrine\DBAL\Types\Type;
    use Doctrine\DBAL\Platforms\AbstractPlatform;

    /**
     * Class UuidIdentifierType
     *
     * @package BeyondCapable\Core\Platform\Persistence\Doctrine\Type
     */
    final class UuidIdentifierType extends Type
    {
        public const NAME = 'uuid_identifier';

        /**
         * @param array<array-key, mixed> $column
         */
        public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
        {
            return 'varchar(36)';
        }

        public function getName(): string
        {
            return self::NAME;
        }

        /**
         * @param UuidIdentifier $value
         */
        public function convertToDatabaseValue($value, AbstractPlatform $platform): string
        {
            return (string) $value;
        }

        /**
         * @param string $value
         */
        public function convertToPHPValue($value, AbstractPlatform $platform): UuidIdentifier
        {
            return UuidIdentifier::createFromString($value);
        }
    }
}
