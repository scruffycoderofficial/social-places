<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\ValueObject\Token
{
    use BeyondCapable\Core\Platform\Domain\ValueObject\Uuid\AbstractUuid;
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use Symfony\Component\Uid\Uuid;

    /**
     * Class UuidToken
     *
     * @package BeyondCapable\Core\Platform\Domain\ValueObject\Token
     */
    class UuidToken extends AbstractUuid
    {
        /**
         * @return UuidToken
         */
        public static function create(): UuidToken
        {
            return new UuidToken(Uuid::v4());
        }

        /**
         * @param string $uuid
         * @return UuidToken
         * @throws InvalidArgumentException
         */
        public static function createFromString(string $uuid): UuidToken
        {
            if (!Uuid::isValid($uuid)) {
                throw new InvalidArgumentException(sprintf('%s is not an Uuid valid.', $uuid));
            }

            return self::createFromUuid(Uuid::fromString($uuid));
        }

        /**
         * @param Uuid $uuid
         * @return UuidToken
         */
        public static function createFromUuid(Uuid $uuid): UuidToken
        {
            return new UuidToken($uuid);
        }

        /**
         * @param UuidToken $token
         * @return bool
         */
        public function equalTo(UuidToken $token): bool
        {
            return (string) $token === (string) $this;
        }
    }
}
