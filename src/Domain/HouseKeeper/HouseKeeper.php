<?php

namespace BeyondCapable\Domain\HouseKeeper;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use BeyondCapable\Domain\HouseKeeper\Event\HouseKeeperSubscribed;

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
        $this->apply(new HouseKeeperSubscribed($keeperId));
    }

    protected function applyHouseKeeperWasSubscribed(HouseKeeperSubscribed $subscribedEvent)
    {
        $this->houseKeeperId = $subscribedEvent->getHouseKeeperId();
    }
}
