<?php

namespace BeyondCapable\Domain\HouseKeeper\Event;

use Broadway\Serializer\SerializationException;
use BeyondCapable\Domain\HouseKeeper\HouseKeeperId;

final class HouseKeeperSubscribed extends HouseKeeperEvent
{
    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data): HouseKeeperSubscribed
    {
        if (!isset($data['houseKeeperId'])) {
            throw new SerializationException('Missing a unique identifying number for subject.');
        }

        return new self(new HouseKeeperId($data['houseKeeperId']));
    }
}