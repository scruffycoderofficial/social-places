<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Persistence\Doctrine\Type
{
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\HashedPassword;

    use Doctrine\DBAL\Types\StringType;
    use Doctrine\DBAL\Platforms\AbstractPlatform;

    /**
     * Class HashedPasswordType
     *
     * @package BeyondCapable\Component\Security\Persistence\Doctrine\Type
     */
    final class HashedPasswordType extends StringType
    {
        public const NAME = 'hashed_password';

        /**
         * @param array<array-key, mixed> $column
         */
        public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
        {
            return 'varchar(255)';
        }

        public function getName(): string
        {
            return self::NAME;
        }

        /**
         * @param HashedPassword $value
         */
        public function convertToDatabaseValue($value, AbstractPlatform $platform): string
        {
            return (string) $value;
        }

        /**
         * @param string $value
         */
        public function convertToPHPValue($value, AbstractPlatform $platform): HashedPassword
        {
            return HashedPassword::createFromString($value);
        }
    }
}
