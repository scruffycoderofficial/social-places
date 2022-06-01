<?php

namespace BeyondCapable\Domain\HouseKeeper;

use InvalidArgumentException;
use Assert\Assertion as Assert;
use Assert\AssertionFailedException;

final class HouseKeeperId
{
    private $keeperId;

    public function __construct(string $keeperId)
    {
        try {

            Assert::uuid($keeperId);

        } catch (AssertionFailedException $e) {

            throw new InvalidArgumentException($e->getMessage());
        }

        $this->keeperId = $keeperId;
    }

    public function __toString(): string
    {
        return $this->keeperId;
    }
}
