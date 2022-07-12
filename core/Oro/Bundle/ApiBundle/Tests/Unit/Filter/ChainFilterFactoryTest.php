<?php

namespace Oro\Bundle\ApiBundle\Tests\Unit\Filter;

use Oro\Bundle\ApiBundle\Filter\ChainFilterFactory;
use Oro\Bundle\ApiBundle\Filter\FilterFactoryInterface;
use Oro\Bundle\ApiBundle\Filter\StandaloneFilter;

class ChainFilterFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testChainFactory()
    {
        $childFactory1 = $this->createMock(FilterFactoryInterface::class);
        $childFactory2 = $this->createMock(FilterFactoryInterface::class);
        $chainFactory = new ChainFilterFactory([$childFactory1, $childFactory2]);

        $knownFilter1 = $this->createMock(StandaloneFilter::class);
        $knownFilter2 = $this->createMock(StandaloneFilter::class);
        $knownFilter31 = $this->createMock(StandaloneFilter::class);
        $knownFilter32 = $this->createMock(StandaloneFilter::class);

        $childFactory1->expects(self::any())
            ->method('createFilter')
            ->willReturnMap(
                [
                    ['known1', [], $knownFilter1],
                    ['known3', ['some_option' => 'val'], $knownFilter31],
                    ['unknown1', [], null]
                ]
            );
        $childFactory2->expects(self::any())
            ->method('createFilter')
            ->willReturnMap(
                [
                    ['known2', [], $knownFilter2],
                    ['known3', ['some_option' => 'val'], $knownFilter32],
                    ['unknown2', [], null]
                ]
            );

        self::assertSame($knownFilter1, $chainFactory->createFilter('known1'));
        self::assertSame($knownFilter2, $chainFactory->createFilter('known2'));
        self::assertSame($knownFilter31, $chainFactory->createFilter('known3', ['some_option' => 'val']));
        self::assertNull($chainFactory->createFilter('unknown1'));
        self::assertNull($chainFactory->createFilter('unknown2'));
    }
}
