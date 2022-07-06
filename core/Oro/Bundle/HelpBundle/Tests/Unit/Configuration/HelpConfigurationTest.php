<?php

namespace Oro\Bundle\HelpBundle\Tests\Unit\Configuration;

use Oro\Bundle\HelpBundle\Configuration\HelpConfiguration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class HelpConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider processConfigurationDataProvider
     */
    public function testProcessConfiguration($options, $expects)
    {
        $processor = new Processor();
        $configuration = new HelpConfiguration();
        $result = $processor->processConfiguration($configuration, [$options]);

        $this->assertEquals($expects, $result);
    }

    public function processConfigurationDataProvider()
    {
        return [
            'minimal_config' => [
                [],
                [
                    'vendors'   => [],
                    'resources' => [],
                    'routes'    => []
                ]
            ],
            'extend_config'  => [
                [
                    'vendors'   => [
                        'Oro' => [
                            'alias' => 'Platform'
                        ]
                    ],
                    'resources' => [
                        'AcmeFooBundle'         => [
                            'server' => 'http://server.com',
                            'prefix' => 'prefix',
                            'alias'  => 'alias',
                            'uri'    => 'uri',
                            'link'   => 'http://server.com/link'
                        ],
                        'AcmeFooBundle:Foo'     => [
                            'server' => 'https://server.com',
                            'prefix' => 'prefix',
                            'alias'  => 'alias',
                            'uri'    => 'uri',
                            'link'   => 'http://server.com/link'
                        ],
                        'AcmeFooBundle:Foo:bar' => [
                            'server' => 'http://server.com',
                            'prefix' => 'prefix',
                            'alias'  => 'alias',
                            'uri'    => 'uri',
                            'link'   => 'http://server.com/link'
                        ]
                    ],
                    'routes'    => [
                        'test_route' => [
                            'server' => 'http://server.com',
                            'uri'    => 'uri',
                            'link'   => 'link'
                        ]
                    ]
                ],
                [
                    'vendors'   => [
                        'Oro' => [
                            'alias' => 'Platform'
                        ]
                    ],
                    'resources' => [
                        'AcmeFooBundle'         => [
                            'server' => 'http://server.com',
                            'prefix' => 'prefix',
                            'alias'  => 'alias',
                            'uri'    => 'uri',
                            'link'   => 'http://server.com/link'
                        ],
                        'AcmeFooBundle:Foo'     => [
                            'server' => 'https://server.com',
                            'prefix' => 'prefix',
                            'alias'  => 'alias',
                            'uri'    => 'uri',
                            'link'   => 'http://server.com/link'
                        ],
                        'AcmeFooBundle:Foo:bar' => [
                            'server' => 'http://server.com',
                            'prefix' => 'prefix',
                            'alias'  => 'alias',
                            'uri'    => 'uri',
                            'link'   => 'http://server.com/link'
                        ]
                    ],
                    'routes'    => [
                        'test_route' => [
                            'server' => 'http://server.com',
                            'uri'    => 'uri',
                            'link'   => 'link'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider processConfigurationFailsDataProvider
     */
    public function testProcessConfigurationFails($options, $expectedException, $expectedExceptionMessage)
    {
        $processor = new Processor();
        $configuration = new HelpConfiguration();

        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $processor->processConfiguration($configuration, [$options]);
    }

    public function processConfigurationFailsDataProvider()
    {
        return [
            'invalid_resource'                 => [
                [
                    'resources' => [
                        '123' => []
                    ]
                ],
                InvalidConfigurationException::class,
                'Node "resources" contains invalid resource name "123".'
            ],
            'invalid_server'                   => [
                [
                    'resources' => [
                        'AcmeFooBundle:Foo:bar' => [
                            'server' => 'server'
                        ]
                    ]
                ],
                InvalidConfigurationException::class,
                'Invalid configuration for path "help.resources.AcmeFooBundle:Foo:bar.server": '
                . 'Invalid URL "server".'
            ],
            'invalid_link'                     => [
                [
                    'resources' => [
                        'AcmeFooBundle:Foo:bar' => [
                            'link' => 'link'
                        ]
                    ]
                ],
                InvalidConfigurationException::class,
                'Invalid configuration for path "help.resources.AcmeFooBundle:Foo:bar.link": Invalid URL "link".'
            ],
            'invalid_vendor_name'              => [
                [
                    'vendors' => [
                        '123' => []
                    ]
                ],
                InvalidConfigurationException::class,
                'Node "vendors" contains invalid vendor name "123".'
            ],
            'invalid_vendor_too_many_sections' => [
                [
                    'resources' => [
                        'AcmeFooBundle:Foo:bar:baz' => []
                    ]
                ],
                InvalidConfigurationException::class,
                'Node "resources" contains invalid resource name "AcmeFooBundle:Foo:bar:baz".'
            ]
        ];
    }
}
