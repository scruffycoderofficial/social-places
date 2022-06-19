<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\ValueObject\Identifier
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Uuid\AbstractUuid;
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use Symfony\Component\Uid\Uuid;

    /**
     * Class UuidIdentifier
     *
     * @package BeyondCapable\Core\Platform\Domain\ValueObject\Identifier
     */
    class UuidIdentifier extends AbstractUuid
    {
        /**
         * @return UuidIdentifier
         */
        public static function create(): UuidIdentifier
        {
            return new UuidIdentifier(Uuid::v4());
        }

        /**
         * @param string $uuid
         * @return UuidIdentifier
         * @throws InvalidArgumentException
         */
        public static function createFromString(string $uuid): UuidIdentifier
        {
            if (!Uuid::isValid($uuid)) {
                throw new InvalidArgumentException(sprintf('%s is not an Uuid valid.', $uuid));
            }

            return self::createFromUuid(Uuid::fromString($uuid));
        }

        /**
         * @param Uuid $uuid
         * @return UuidIdentifier
         */
        public static function createFromUuid(Uuid $uuid): UuidIdentifier
        {
            return new UuidIdentifier($uuid);
        }
    }

}