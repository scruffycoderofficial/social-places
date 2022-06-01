<?php

namespace BeyondCapable\Domain\HouseKeeper;

use Broadway\EventSourcing\EventSourcedAggregateRoot;

class HouseKeeper extends EventSourcedAggregateRoot
{
    private $houseId;

    public function getAggregateRootId(): string
    {
        return (string) $this->basketId;
    }
}
