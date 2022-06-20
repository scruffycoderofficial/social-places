<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Persistence\Doctrine\Type
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Email\EmailAddress;

    use Doctrine\DBAL\Types\StringType;
    use Doctrine\DBAL\Platforms\AbstractPlatform;

    /**
     * Class EmailAddressType
     *
     * @package BeyondCapable\Core\Platform\Persistence\Doctrine\Type
     */
    final class EmailAddressType extends StringType
    {
        public const NAME = 'email_address';

        /**
         * @param array<array-key, bool|float|string|int|null> $column
         */
        public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
        {
            return sprintf('varchar(%d)', $column['length']);
        }

        public function getName(): string
        {
            return self::NAME;
        }

        /**
         * @param EmailAddress $value
         */
        public function convertToDatabaseValue($value, AbstractPlatform $platform): string
        {
            return (string) $value;
        }

        /**
         * @param string $value
         */
        public function convertToPHPValue($value, AbstractPlatform $platform): EmailAddress
        {
            return EmailAddress::createFromString($value);
        }
    }
}
