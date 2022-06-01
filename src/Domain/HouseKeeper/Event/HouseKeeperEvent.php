<?php

namespace BeyondCapable\Domain\HouseKeeper\Event;

use Broadway\Serializer\Serializable;
use BeyondCapable\Domain\HouseKeeper\HouseKeeperId;

abstract class HouseKeeperEvent implements Serializable
{
    private $keeperId;

    public function __construct(HouseKeeperId $keeperId)
    {
        $this->keeperId = $keeperId;
    }

    public function getHouseKeeperId(): HouseKeeperId
    {
        return $this->keeperId;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): array
    {
        return ['houseKeeperId' => (string) $this->keeperId];
    }
}