<?php

namespace Oro\Bundle\NavigationBundle\Tests\Unit\Title\TitleReader;

use Oro\Bundle\NavigationBundle\Tests\Unit\Title\TitleReader\Stub\TitleReaderStub;
use Oro\Bundle\NavigationBundle\Title\TitleReader\TitleReaderRegistry;

class TitleReaderRegistryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider titleReaderProvider
     *
     * @param TitleReaderStub[] $readers
     * @param string            $routeName
     * @param string            $expectedTitle
     */
    public function testGetTitleByRoute(array $readers, $routeName, $expectedTitle)
    {
        $registry = new TitleReaderRegistry($readers);

        $this->assertEquals($expectedTitle, $registry->getTitleByRoute($routeName));
    }

    /**
     * @return array
     */
    public function titleReaderProvider()
    {
        return [
            'no readers' => [
                'readers' => [],
                'routeName' => 'route_name',
                'expectedTitle' => null,
            ],
            'one reader' => [
                'readers' => [
                    new TitleReaderStub(['route_name' => 'Route Name'])
                ],
                'routeName' => 'route_name',
                'expectedTitle' => 'Route Name',
            ],
            'two readers with different route names' => [
                'readers' => [
                    new TitleReaderStub(['route_name' => 'Route Name']),
                    new TitleReaderStub(['new_route_name' => 'New Route Name'])
                ],
                'routeName' => 'route_name',
                'expectedTitle' => 'Route Name',
            ],
            'two readers with same route names' => [
                'readers' => [
                    new TitleReaderStub(['route_name' => 'Route Name']),
                    new TitleReaderStub(['route_name' => 'New Route Name'])
                ],
                'routeName' => 'route_name',
                'expectedTitle' => 'Route Name'
            ],
            'two readers without coincidence ' => [
                'readers' => [
                    new TitleReaderStub(['route_name' => 'Route Name']),
                    new TitleReaderStub(['route_name' => 'New Route Name'])
                ],
                'routeName' => 'route_name_another',
                'expectedTitle' => null
            ],
        ];
    }
}
