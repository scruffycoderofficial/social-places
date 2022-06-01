<?php

namespace spec\BeyondCapable\Domain\HouseKeeper;

use PhpSpec\ObjectBehavior;
use BeyondCapable\Domain\HouseKeeper\HouseKeeper;
use Broadway\EventSourcing\EventSourcedAggregateRoot;

class HouseKeeperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(HouseKeeper::class);
    }

    function it_is_an_event_sourced_aggregate_root()
    {
        $this->shouldBeAnInstanceOf(EventSourcedAggregateRoot::class);
    }

    function it_can_subscribe_the_house_it_keeps()
    {
        $
    }
}
