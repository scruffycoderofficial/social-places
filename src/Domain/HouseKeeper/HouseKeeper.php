<?php

namespace BeyondCapable\Domain\HouseKeeper;

use Broadway\EventSourcing\EventSourcedAggregateRoot;

class HouseKeeper extends EventSourcedAggregateRoot
{
    private $houseKeeperId;

    public function getAggregateRootId(): string
    {
        return (string) $this->houseKeeperId;
    }

    public static function subscribeHouseKeeper(HouseKeeperId $keeperId): HouseKeeper
    {
        $houseKeeper = new HouseKeeper();
        $houseKeeper->subscribeKeeper($keeperId);

        return $houseKeeper;
    }

    private function subscribeKeeper(HouseKeeperId $keeperId)
    {

    }
}
