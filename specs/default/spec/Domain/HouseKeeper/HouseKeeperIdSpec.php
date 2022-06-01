<?php

namespace spec\BeyondCapable\Domain\HouseKeeper;

use Ramsey\Uuid\Uuid;
use PhpSpec\ObjectBehavior;
use BeyondCapable\Domain\HouseKeeper\HouseKeeperId;

class HouseKeeperIdSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Uuid::uuid4());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HouseKeeperId::class);
    }
}
