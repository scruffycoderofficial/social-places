<?php

declare(strict_types=1);

namespace BeyondCapable\Domain\ValueObject\Uuid
{
    use Stringable;
    use Symfony\Component\Uid\Uuid;

    /**
     * Class AbstractUuid
     *
     * @package BeyondCapable\Domain\ValueObject\Uuid
     */
    abstract class AbstractUuid implements Stringable
    {
        /**
         * AbstractUuid constructor.
         * @param Uuid $uuid
         */
        protected function __construct(private Uuid $uuid)
        {
        }

        /**
         * @return string
         */
        public function __toString(): string
        {
            return (string) $this->uuid;
        }

        /**
         * @return AbstractUuid
         */
        abstract public static function create(): AbstractUuid;

        /**
         * @param string $uuids
         * @return AbstractUuid
         */
        abstract public static function createFromString(string $uuid): AbstractUuid;

        /**
         * @param Uuid $uuid
         * @return AbstractUuid
         */
        abstract public static function createFromUuid(Uuid $uuid): AbstractUuid;

        /**
         * @return Uuid
         */
        public function uuid(): Uuid
        {
            return $this->uuid;
        }
    }
}
