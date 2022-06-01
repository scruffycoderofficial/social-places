<?php

namespace spec\BeyondCapable\Domain\HouseKeeper\Event;

use Ramsey\Uuid\Uuid;
use PhpSpec\ObjectBehavior;
use BeyondCapable\Domain\HouseKeeper\HouseKeeperId;
use BeyondCapable\Domain\HouseKeeper\Event\HouseKeeperEvent;
use BeyondCapable\Domain\HouseKeeper\Event\HouseKeeperSubscribed;

class HouseKeeperSubscribedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new HouseKeeperId(Uuid::uuid4()));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HouseKeeperSubscribed::class);
    }

    function it_should_be_an_event()
    {
        $this->shouldBeAnInstanceOf(HouseKeeperEvent::class);
    }

    function it_can_serialize_event()
    {
        $this->serialize()->shouldBeArray();
    }

    function it_can_deserialize_event()
    {
        $this->deserialize(['houseKeeperId' => Uuid::uuid4()])->shouldBeAnInstanceOf(HouseKeeperSubscribed::class);
    }
}